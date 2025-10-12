<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ApiResponseTrait;

abstract class BaseFormRequest extends FormRequest
{
    use ApiResponseTrait;
    /**
     * Determine if the user is authorized to make this request.
     * You can override this in child requests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     *
     * For API requests, return JSON. For web, redirect back with errors.
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                $this->errorResponse(
                    'Validation failed.',
                    422,
                    $validator->errors()
                )
            );
        }

        parent::failedValidation($validator); // default Laravel redirect for web
    }

    /**
     * Default messages can be overridden in child requests
     */
    public function messages(): array
    {
        return [
            // Example default messages
            'required' => 'The :attribute field is required.',
            'email' => 'The :attribute must be a valid email address.',
            'max' => 'The :attribute may not be greater than :max characters.',
        ];
    }

    /**
     * Default attribute names can be overridden in child requests
     */
    public function attributes(): array
    {
        return [
            // Example: rename attributes for friendly messages
            'email' => 'Email Address',
            'password' => 'Password',
        ];
    }
}
