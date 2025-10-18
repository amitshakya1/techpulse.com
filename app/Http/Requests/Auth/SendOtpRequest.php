<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class SendOtpRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'country_code' => ['nullable', 'string', 'max:5'], // optional, if you store +91 etc.
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'phone.required' => 'Please enter your phone number.',
            'phone.string' => 'Phone number must be a valid string.',
            'phone.regex' => 'Please enter a valid phone number (10–15 digits).',

            'country_code.string' => 'Country code must be a valid string.',
            'country_code.max' => 'Country code cannot be longer than 5 characters.',
        ];
    }
}
