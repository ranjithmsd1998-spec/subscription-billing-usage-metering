<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsageEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'merchant' => [
                'id' => $this->merchant?->id,
                'name' => $this->merchant?->name,
                'code' => $this->merchant?->code,
            ],

            'customer' => [
                'id' => $this->customer?->id,
                'name' => $this->customer?->name,
                'email' => $this->customer?->email,
            ],

            'subscription' => [
                'id' => $this->subscription?->id,
                'status' => $this->subscription?->status,
            ],

            'subscription_period' => [
                'id' => $this->subscriptionPeriod?->id,
                'starts_at' => $this->subscriptionPeriod?->starts_at?->toISOString(),
                'ends_at' => $this->subscriptionPeriod?->ends_at?->toISOString(),
            ],

            'idempotency_key' => $this->idempotency_key,

            'usage_units' => $this->usage_units,

            'unit_name' => $this->unit_name,

            'occurred_at' => $this->occurred_at?->toISOString(),

            'metadata' => $this->metadata,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}