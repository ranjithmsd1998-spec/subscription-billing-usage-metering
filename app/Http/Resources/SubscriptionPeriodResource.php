<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPeriodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'subscription' => [
                'id' => $this->subscription?->id,
                'customer_id' => $this->subscription?->customer_id,
                'status' => $this->subscription?->status,
            ],

            'plan' => [
                'id' => $this->plan?->id,
                'name' => $this->plan?->name,
                'code' => $this->plan?->code,
            ],

            'starts_at' => $this->starts_at?->toISOString(),
            'ends_at' => $this->ends_at?->toISOString(),

            /*
             * These values are historical pricing snapshots.
             */
            'base_price' => $this->base_price,
            'included_units' => $this->included_units,
            'overage_rate' => $this->overage_rate,
            'billing_cycle' => $this->billing_cycle,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}