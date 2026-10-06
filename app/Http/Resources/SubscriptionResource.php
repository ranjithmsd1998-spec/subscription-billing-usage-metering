<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
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

            'plan' => [
                'id' => $this->plan?->id,
                'name' => $this->plan?->name,
                'code' => $this->plan?->code,
                'base_price' => $this->plan?->base_price,
                'billing_cycle' => $this->plan?->billing_cycle,
            ],

            'status' => $this->status,

            'started_at' => $this->started_at?->toISOString(),

            'current_period_start' =>
                $this->current_period_start?->toISOString(),

            'current_period_end' =>
                $this->current_period_end?->toISOString(),

            'cancelled_at' =>
                $this->cancelled_at?->toISOString(),

            'created_at' => $this->created_at?->toISOString(),

            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}