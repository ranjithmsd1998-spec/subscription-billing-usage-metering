<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class StoreMerchantRequest extends FormRequest
{
    /**
     * Determine whether the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:merchants,email',
            ],

            'code' => [
                'required',
                'max:255',
                'unique:merchants,code',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'status' => [
                'required',
                'string',
                'in:active,inactive',
            ],

            'metadata' => [
                'nullable',
                'array',
            ],
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Merchant name is required.',

            'code.required' => 'Merchant Code is required.',

            'email.required' => 'Merchant email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'A merchant with this email already exists.',

            'phone.max' => 'Phone number may not exceed 30 characters.',

            'status.required' => 'Merchant status is required.',
            'status.in' => 'Merchant status must be either active or inactive.',

            'metadata.array' => 'Metadata must be a valid object.',
        ];
    }
}