<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'merchant_id' => $this->merchant_id,
            'customer_id' => $this->customer_id,
            'subscription_id' => $this->subscription_id,
            'subscription_period_id' => $this->subscription_period_id,

            'invoice_number' => $this->invoice_number,

            'billing_period_start' => $this->billing_period_start?->toISOString(),
            'billing_period_end' => $this->billing_period_end?->toISOString(),

            'base_amount' => $this->base_amount,
            'overage_amount' => $this->overage_amount,
            'proration_amount' => $this->proration_amount,

            'subtotal' => $this->subtotal,

            'tax_rate' => $this->tax_rate,
            'tax_amount' => $this->tax_amount,

            'total' => $this->total,

            'currency' => $this->currency,

            'status' => $this->status,

            'issued_at' => $this->issued_at?->toISOString(),
            'due_at' => $this->due_at?->toISOString(),
            'paid_at' => $this->paid_at?->toISOString(),

            'metadata' => $this->metadata,

            'items' => InvoiceItemResource::collection(
                $this->whenLoaded('items')
            ),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}