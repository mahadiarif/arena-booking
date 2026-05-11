<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'min:2', 'max:150'],
            'phone'        => ['required', 'string', 'regex:/^01[3-9]\d{8}$/', 'unique:customers,phone'],
            'email'        => ['nullable', 'email', 'max:100'],
            'nid'          => ['nullable', 'string', 'max:30'],
            'organization' => ['nullable', 'string', 'max:150'],
            'address'      => ['nullable', 'string', 'max:500'],
            'notes'        => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'Customer name is required.',
            'name.min'        => 'Name must be at least 2 characters.',
            'phone.required'  => 'Phone number is required.',
            'phone.unique'    => 'This phone number is already registered.',
            'phone.regex'     => 'Phone must be a valid Bangladesh number (e.g. 01712345678).',
            'email.email'     => 'Please enter a valid email address.',
        ];
    }
}
