<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // Personal Information
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['required', 'string', 'max:20', 'unique:users,mobile'],
            
            // Company Information
            'company_name' => ['required', 'string', 'max:255'],
            'company_registration_number' => ['nullable', 'string', 'max:50'],
            'vat_number' => ['nullable', 'string', 'max:50'],
            
            // Address
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state_province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country_code' => ['required', 'string', 'size:2'],
            
            // Password
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            
            // Terms
            'terms' => ['required', 'accepted'],
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'Your first name is required.',
            'last_name.required' => 'Your last name is required.',
            'email.required' => 'Your email address is required.',
            'email.unique' => 'This email is already registered.',
            'mobile.required' => 'Your mobile number is required.',
            'mobile.unique' => 'This mobile number is already registered.',
            'company_name.required' => 'Your company name is required.',
            'address_line_1.required' => 'Your street address is required.',
            'city.required' => 'Your city is required.',
            'postal_code.required' => 'Your postal code is required.',
            'country_code.required' => 'Your country is required.',
            'password.required' => 'Please create a password.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Passwords do not match.',
            'terms.accepted' => 'You must accept the terms and conditions.',
        ];
    }
}