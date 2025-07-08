<?php

namespace App\Http\Requests;

use App\Http\Requests\CustomFormRequest;
use App\Enums\Role;

class NewUserRequest extends CustomFormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'string', "in:$role_values"],
        ];
    }
}
