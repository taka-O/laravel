<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Api\SendResetPasswordRequest;
use App\Http\Requests\Api\ResetPasswordRequest;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'sendResetPassword', 'resetPassword']]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        try {
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        $user = auth()->user();

        return response()->json([
            'user' => $user,
            'token' => $token,
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ], 200);
    }

    public function sendResetPassword(SendResetPasswordRequest $request)
    {
        $reset_url = $request->get('reset_url');

        $status = Password::sendResetLink(
            $request->only('email'),
            function($user, $token) use ($reset_url) {
                $user->sendPasswordResetMail($token, $reset_url);
            }
        );

        return $status == Password::RESET_LINK_SENT
            ? response()->noContent()
            : response()->json(['message' => 'パスワード再設定メールを送信できませんでした。'], 401);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
    
                $user->save();
    
                event(new PasswordReset($user));
            }
        );
    
        return $status === Password::PASSWORD_RESET
            ? response()->noContent()
            : response()->json(['message' => 'パスワードを更新できませんでした。'], 401);
    }

    public function logout()
    {
        auth()->logout();

        return response()->noContent();
    }

    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }

}