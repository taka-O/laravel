<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UserRequest;
use App\Models\User;
use App\Enums\Role;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse {
        $userQuery = User::query();
        $role = $request->query('role');
        if ($role) {
            $userQuery = $userQuery->where('role_type', Role::getByName($role)->value);
        }
        $name = $request->query('name');
        if ($name) {
            $userQuery = $userQuery->where('name', 'like', "%$name%");
        }

        $users = $userQuery->get();
        return response()->json($users);
    }

    public function store(UserRequest $request): JsonResponse {
        $user = new User;
        $user->createNewUser($request->only(['name', 'email', 'role']));

        return response()->json($user, 201);
    }

    public function update(UserRequest $request, string $id): JsonResponse {
        try {
            $user = User::findOrFail($id);
            $user->updateUser($request->only(['name', 'email', 'role']));

            return response()->json($user, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message" => "User Not Found"
            ], 404);
        }
    }
}
