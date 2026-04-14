<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class ApiRequest extends FormRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'The given data was invalid.',
            'errors' => $validator->errors(),
        ], 422));
    }

    public static function notFound(string $message = null): never
    {
        throw new HttpResponseException(response()->json([
            'message' => $message ?? 'Not found' ,
        ], 404 , [
            'Content-Type' => 'application/json',
        ], JSON_UNESCAPED_UNICODE));
    }
}
