<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\admin\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\NewUserRequest;
use App\Models\User;
use App\Enums\Role;

class UserController extends Controller
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

    public function create(NewUserRequest $request) {
        $user = new User;
        $user->createNewUser($request->only(['name', 'email', 'role']));

        return response()->json([
            "message" => "student record created"
        ], 201);
    }
}
