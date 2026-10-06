<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer');

        return [
            'merchant_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:merchants,id',
            ],

            'code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('customers', 'code')
                    ->where(
                        fn ($query) => $query->where(
                            'merchant_id',
                            $this->input(
                                'merchant_id',
                                optional($this->route('customer'))->merchant_id
                            )
                        )
                    )
                    ->ignore($customerId),
            ],

            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
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
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],

            'metadata' => [
                'sometimes',
                'nullable',
                'array',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'merchant_id.required' => 'Merchant ID is required.',
            'merchant_id.exists' => 'The selected merchant does not exist.',

            'code.required' => 'Customer code is required.',
            'code.unique' => 'The customer code already exists for this merchant.',

            'name.required' => 'Customer name is required.',

            'email.email' => 'Please provide a valid email address.',

            'phone.max' => 'Phone number may not exceed 20 characters.',

            'status.required' => 'Customer status is required.',
            'status.in' => 'Customer status must be either active or inactive.',

            'metadata.array' => 'Metadata must be a valid object.',
        ];
    }
}