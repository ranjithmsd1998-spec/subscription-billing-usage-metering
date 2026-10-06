<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'merchant_id' => [
                'required',
                'integer',
                'exists:merchants,id',
            ],

            'customer_id' => [
                'required',
                'integer',
                'exists:customers,id',
            ],

            'plan_id' => [
                'required',
                'integer',
                'exists:plans,id',
            ],

            'status' => [
                'sometimes',
                Rule::in([
                    'active',
                    'cancelled',
                    'expired',
                ]),
            ],

            'started_at' => [
                'required',
                'date',
            ],

            'current_period_start' => [
                'required',
                'date',
            ],

            'current_period_end' => [
                'required',
                'date',
                'after_or_equal:current_period_start',
            ],

            'cancelled_at' => [
                'sometimes',
                'nullable',
                'date',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'merchant_id.required' => 'Merchant is required.',
            'merchant_id.integer' => 'Merchant ID must be a valid integer.',
            'merchant_id.exists' => 'The selected merchant does not exist.',

            'customer_id.required' => 'Customer is required.',
            'customer_id.integer' => 'Customer ID must be a valid integer.',
            'customer_id.exists' => 'The selected customer does not exist.',

            'plan_id.required' => 'Plan is required.',
            'plan_id.integer' => 'Plan ID must be a valid integer.',
            'plan_id.exists' => 'The selected plan does not exist.',

            'status.in' => 'Subscription status must be active, cancelled, or expired.',

            'started_at.required' => 'Subscription start date is required.',
            'started_at.date' => 'Subscription start date must be a valid date.',

            'current_period_start.required' => 'Current period start date is required.',
            'current_period_start.date' => 'Current period start date must be a valid date.',

            'current_period_end.required' => 'Current period end date is required.',
            'current_period_end.date' => 'Current period end date must be a valid date.',
            'current_period_end.after_or_equal' =>
                'Current period end must be after or equal to the current period start.',

            'cancelled_at.date' => 'Cancelled date must be a valid date.',
        ];
    }
}