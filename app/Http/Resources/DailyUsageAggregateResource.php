<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyUsageAggregateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'merchant_id' => $this->merchant_id,
            'customer_id' => $this->customer_id,
            'subscription_id' => $this->subscription_id,
            'subscription_period_id' => $this->subscription_period_id,

            'usage_date' => $this->usage_date?->format('Y-m-d'),

            'total_units' => $this->total_units,
            'event_count' => $this->event_count,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}