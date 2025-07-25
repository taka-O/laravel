<?php

namespace Tests\Feature\Http\Controllers\AuthController;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use App\Notifications\ResetPasswordNotification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * メールアドレスとトークンで認証を行う（成功）
     */
    public function testPasswordReset()
    {
        Notification::fake();

        $user = User::factory()->create();
        $token = $this->passwordRequest($user);

        // パスワードをリセットする
        $newPassword = $this->faker->password(8, 15);
        $params = [
            'email' => $user->email,
            'token' => $token,
            'password' => $newPassword,
            'password_confirmation' => $newPassword

        ];
        $response = $this->post('/api/auth/reset_password', $params);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     * トークンが異なる
     */
    public function testPasswordResetWithInvalidToken()
    {
        Notification::fake();

        $user = User::factory()->create();
        $token = $this->passwordRequest($user);

        // パスワードをリセットする
        $newPassword = $this->faker->password(8, 15);
        $params = [
            'email' => $user->email,
            'token' => $this->faker->word(10),
            'password' => $newPassword,
            'password_confirmation' => $newPassword

        ];
        $response = $this->post('/api/auth/reset_password', $params);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    private function passwordRequest(User $user)
    {
        $generated_token = '';

        Password::sendResetLink(
            ['email' => $user->email],
            function($user, $token) use (&$generated_token) {
                $generated_token = $token;
            }
        );

        return $generated_token;
    }
}
