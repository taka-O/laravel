<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CustomFormRequest extends FormRequest
{
  protected function failedValidation(Validator $validator): ValidationException
  {
    $response = new JsonResponse([
        'status' => 'error',
        'message' => 'validation error',
        'errors' => $validator->errors(),
    ], 422);

    throw new ValidationException($validator, $response);
  }
}
