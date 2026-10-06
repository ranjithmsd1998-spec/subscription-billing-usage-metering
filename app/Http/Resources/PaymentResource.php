<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'merchant_id' => $this->merchant_id,

            'customer_id' => $this->customer_id,

            'invoice_id' => $this->invoice_id,

            'payment_reference' => $this->payment_reference,

            'amount' => $this->amount,

            'currency' => $this->currency,

            'payment_method' => $this->payment_method,

            'status' => $this->status,

            'paid_at' => $this->paid_at,

            'metadata' => $this->metadata,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}