<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'merchant_id' => $this->merchant_id,

            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,

            'currency' => $this->currency,
            'base_price' => $this->base_price,
            'billing_cycle' => $this->billing_cycle,

            'included_units' => $this->included_units,
            'overage_rate' => $this->overage_rate,
            'unit_name' => $this->unit_name,

            'status' => $this->status,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}