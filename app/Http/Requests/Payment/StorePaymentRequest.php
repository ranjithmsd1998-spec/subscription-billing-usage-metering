<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
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

            'invoice_id' => [
                'required',
                'integer',
                'exists:invoices,id',
            ],

            'payment_reference' => [
                'required',
                'string',
                'max:255',
                Rule::unique('payments', 'payment_reference')
                    ->where(
                        fn ($query) => $query->where(
                            'merchant_id',
                            $this->merchant_id
                        )
                    ),
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'currency' => [
                'required',
                'string',
                'size:3',
            ],

            'payment_method' => [
                'required',
                'string',
                Rule::in([
                    'cash',
                    'bank_transfer',
                    'card',
                    'upi',
                    'other',
                ]),
            ],

            'status' => [
                'sometimes',
                'string',
                Rule::in([
                    'pending',
                    'completed',
                ]),
            ],

            'paid_at' => [
                'nullable',
                'date',
            ],

            'metadata' => [
                'nullable',
                'array',
            ],
        ];
    }
}