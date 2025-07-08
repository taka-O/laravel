<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Enums\Role;

class UsersController extends Controller
{
    public function index(Request $request) {
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

    public function create(Request $request) {
        // バリデーション処理
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'role' => array_column(Role::cases(), 'name'),
        ]);

        $user = new User;
        $user->createUser($request->only(['name', 'email', 'role']));

        return response()->json([
            "message" => "student record created"
        ], 201);
    }
}
