<?php

namespace App\Http\Requests\UsageEvent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsageEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usage_units' => [
                'sometimes',
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
                'sometimes',
                'required',
                'date',
            ],

            'metadata' => [
                'sometimes',
                'nullable',
                'array',
            ],
        ];
    }
}