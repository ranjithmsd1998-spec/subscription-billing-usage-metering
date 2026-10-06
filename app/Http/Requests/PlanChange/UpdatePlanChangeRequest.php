<?php

namespace App\Http\Requests\PlanChange;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlanChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subscription_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:subscriptions,id',
            ],

            'from_plan_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:plans,id',
            ],

            'to_plan_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:plans,id',
            ],

            'effective_at' => [
                'sometimes',
                'required',
                'date',
            ],

            'change_type' => [
                'sometimes',
                'required',
                Rule::in([
                    'upgrade',
                    'downgrade',
                ]),
            ],

            'reason' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
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
            'subscription_id.required' =>
                'Subscription is required.',

            'subscription_id.integer' =>
                'Subscription ID must be a valid integer.',

            'subscription_id.exists' =>
                'The selected subscription does not exist.',

            'from_plan_id.integer' =>
                'Source plan ID must be a valid integer.',

            'from_plan_id.exists' =>
                'The selected source plan does not exist.',

            'to_plan_id.required' =>
                'Destination plan is required.',

            'to_plan_id.integer' =>
                'Destination plan ID must be a valid integer.',

            'to_plan_id.exists' =>
                'The selected destination plan does not exist.',

            'effective_at.required' =>
                'Effective date is required.',

            'effective_at.date' =>
                'Effective date must be a valid date.',

            'change_type.required' =>
                'Change type is required.',

            'change_type.in' =>
                'Change type must be either upgrade or downgrade.',

            'reason.string' =>
                'Reason must be a valid string.',

            'reason.max' =>
                'Reason may not exceed 255 characters.',

            'metadata.array' =>
                'Metadata must be a valid JSON object.',
        ];
    }
}