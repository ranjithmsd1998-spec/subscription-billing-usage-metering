<?php

namespace App\Http\Requests\DailyUsageAggregate;

use Illuminate\Foundation\Http\FormRequest;

class GenerateDailyUsageAggregateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subscription_period_id' => [
                'required',
                'integer',
                'exists:subscription_periods,id',
            ],

            'usage_date' => [
                'required',
                'date',
            ],
        ];
    }
}