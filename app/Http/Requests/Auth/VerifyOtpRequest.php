<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class VerifyOtpRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'otp' => ['required', 'digits_between:4,8'], // supports 4–8 digit OTPs
            'country_code' => ['nullable', 'string', 'max:5'],
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

            'otp.required' => 'Please enter the OTP sent to your phone.',
            'otp.digits_between' => 'OTP must be between 4 to 8 digits.',

            'country_code.string' => 'Country code must be a valid string.',
            'country_code.max' => 'Country code cannot exceed 5 characters.',
        ];
    }
}
