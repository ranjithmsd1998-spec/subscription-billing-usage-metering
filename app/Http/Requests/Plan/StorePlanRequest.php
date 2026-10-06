<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    /**
     * Determine whether the user is authorized
     * to make this request.
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
            'merchant_id' => [
                'required',
                'integer',
                'exists:merchants,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'currency' => [
                'required',
                'string',
                'size:3',
            ],

            'base_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'billing_cycle' => [
                'required',
                'string',
                'in:monthly,yearly',
            ],

            'included_units' => [
                'required',
                'integer',
                'min:0',
            ],

            'overage_rate' => [
                'required',
                'numeric',
                'min:0',
            ],

            'unit_name' => [
                'required',
                'string',
                'max:100',
            ],

            'status' => [
                'required',
                'string',
                'in:active,inactive',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'merchant_id.required' => 'Merchant is required.',
            'merchant_id.integer' => 'Merchant ID must be a valid integer.',
            'merchant_id.exists' => 'The selected merchant does not exist.',

            'name.required' => 'Plan name is required.',
            'name.string' => 'Plan name must be a valid string.',
            'name.max' => 'Plan name may not exceed 255 characters.',

            'code.required' => 'Plan code is required.',
            'code.string' => 'Plan code must be a valid string.',
            'code.max' => 'Plan code may not exceed 255 characters.',

            'description.string' => 'Plan description must be a valid string.',

            'currency.required' => 'Currency is required.',
            'currency.string' => 'Currency must be a valid string.',
            'currency.size' => 'Currency must contain exactly 3 characters.',

            'base_price.required' => 'Base price is required.',
            'base_price.numeric' => 'Base price must be a valid number.',
            'base_price.min' => 'Base price cannot be negative.',

            'billing_cycle.required' => 'Billing cycle is required.',
            'billing_cycle.in' => 'Billing cycle must be either monthly or yearly.',

            'included_units.required' => 'Included units are required.',
            'included_units.integer' => 'Included units must be a valid integer.',
            'included_units.min' => 'Included units cannot be negative.',

            'overage_rate.required' => 'Overage rate is required.',
            'overage_rate.numeric' => 'Overage rate must be a valid number.',
            'overage_rate.min' => 'Overage rate cannot be negative.',

            'unit_name.required' => 'Unit name is required.',
            'unit_name.string' => 'Unit name must be a valid string.',
            'unit_name.max' => 'Unit name may not exceed 100 characters.',

            'status.required' => 'Plan status is required.',
            'status.in' => 'Plan status must be either active or inactive.',
        ];
    }
}