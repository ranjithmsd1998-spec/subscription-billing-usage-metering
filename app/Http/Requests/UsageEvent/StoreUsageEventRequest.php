<?php

namespace App\Http\Requests\UsageEvent;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsageEventRequest extends FormRequest
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

            'subscription_id' => [
                'required',
                'integer',
                'exists:subscriptions,id',
            ],

            'subscription_period_id' => [
                'required',
                'integer',
                'exists:subscription_periods,id',
            ],

            'idempotency_key' => [
                'required',
                'string',
                'max:255',
            ],

            'usage_units' => [
                'required',
                'integer',
                'min:1',
            ],

            'unit_name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'occurred_at' => [
                'required',
                'date',
            ],

            'metadata' => [
                'nullable',
                'array',
            ],
        ];
    }
}