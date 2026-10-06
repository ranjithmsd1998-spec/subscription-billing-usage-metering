<?php

namespace App\Http\Requests\SubscriptionPeriod;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'starts_at' => [
                'sometimes',
                'required',
                'date',
            ],

            'ends_at' => [
                'sometimes',
                'nullable',
                'date',
                'after:starts_at',
            ],
        ];
    }
}