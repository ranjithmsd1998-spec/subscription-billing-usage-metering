<?php

namespace App\Http\Requests\SubscriptionPeriod;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subscription_id' => [
                'required',
                'integer',
                'exists:subscriptions,id',
            ],

            'starts_at' => [
                'required',
                'date',
            ],

            'ends_at' => [
                'nullable',
                'date',
                'after:starts_at',
            ],
        ];
    }
}