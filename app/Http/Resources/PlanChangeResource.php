<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanChangeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'subscription_id' => $this->subscription_id,

            'from_plan_id' => $this->from_plan_id,

            'to_plan_id' => $this->to_plan_id,

            'effective_at' => $this->effective_at?->toISOString(),

            'change_type' => $this->change_type,

            'reason' => $this->reason,

            'metadata' => $this->metadata,

            'subscription' => $this->whenLoaded(
                'subscription',
                fn () => [
                    'id' => $this->subscription->id,
                    'status' => $this->subscription->status,
                ]
            ),

            'from_plan' => $this->whenLoaded(
                'fromPlan',
                fn () => $this->fromPlan ? [
                    'id' => $this->fromPlan->id,
                    'name' => $this->fromPlan->name,
                    'code' => $this->fromPlan->code,
                    'base_price' => $this->fromPlan->base_price,
                    'currency' => $this->fromPlan->currency,
                ] : null
            ),

            'to_plan' => $this->whenLoaded(
                'toPlan',
                fn () => [
                    'id' => $this->toPlan->id,
                    'name' => $this->toPlan->name,
                    'code' => $this->toPlan->code,
                    'base_price' => $this->toPlan->base_price,
                    'currency' => $this->toPlan->currency,
                ]
            ),

            'created_at' => $this->created_at?->toISOString(),

            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}