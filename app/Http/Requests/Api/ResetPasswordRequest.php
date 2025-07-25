<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\CustomFormRequest;

class ResetPasswordRequest extends CustomFormRequest
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
        return [
            'token' => ['required'],
            'password' => ['required', 'confirmed', 'regex:/\S{8,}/'],
        ];
    }
}
