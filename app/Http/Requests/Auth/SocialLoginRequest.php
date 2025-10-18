<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class SocialLoginRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'provider' => ['required', 'in:google,facebook'],
            'access_token' => ['required', 'string'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'provider.required' => 'The social provider is required.',
            'provider.in' => 'The selected provider is invalid. Only Google or Facebook are allowed.',

            'access_token.required' => 'The access token is required.',
            'access_token.string' => 'The access token must be a valid string.',
        ];
    }
}
