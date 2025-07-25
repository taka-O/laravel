<?php

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\CustomFormRequest;
use App\Enums\Role;

class UserRequest extends CustomFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $role_values = implode(",", array_values(array_column(Role::cases(), 'name')));
        $roles = $role_values . "," . strtoupper($role_values);
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'string', "in:$roles"],
        ];
    }
}
