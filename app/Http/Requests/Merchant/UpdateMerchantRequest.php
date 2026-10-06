<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMerchantRequest extends FormRequest
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
        $merchantId = $this->route('merchant');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('merchants', 'email')
                    ->ignore($merchantId),
            ],

            'code' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('merchants', 'code')
                    ->ignore($merchantId),
            ],

            'phone' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
            ],

            'status' => [
                'sometimes',
                'required',
                'string',
                'in:active,inactive',
            ],

            'metadata' => [
                'sometimes',
                'nullable',
                'array',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Merchant name is required.',

            'email.required' => 'Merchant email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'The email has already been taken.',

            'code.required' => 'Merchant code is required.',
            'code.unique' => 'The code has already been taken.',

            'phone.max' => 'Phone number may not exceed 20 characters.',

            'status.required' => 'Merchant status is required.',
            'status.in' => 'Merchant status must be either active or inactive.',

            'metadata.array' => 'Metadata must be a valid object.',
        ];
    }
}