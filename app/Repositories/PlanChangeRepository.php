<?php

namespace App\Repositories;

use App\Models\PlanChange;
use Illuminate\Database\Eloquent\Collection;

class PlanChangeRepository implements PlanChangeRepositoryInterface
{
    public function getAll(array $filters = []): Collection
    {
        $query = PlanChange::query()
            ->with([
                'subscription',
                'fromPlan',
                'toPlan',
            ])
            ->orderByDesc('effective_at');

        if (!empty($filters['subscription_id'])) {
            $query->where(
                'subscription_id',
                $filters['subscription_id']
            );
        }

        if (!empty($filters['from_plan_id'])) {
            $query->where(
                'from_plan_id',
                $filters['from_plan_id']
            );
        }

        if (!empty($filters['to_plan_id'])) {
            $query->where(
                'to_plan_id',
                $filters['to_plan_id']
            );
        }

        if (!empty($filters['change_type'])) {
            $query->where(
                'change_type',
                $filters['change_type']
            );
        }

        return $query->get();
    }

    public function findById(int $id): ?PlanChange
    {
        return PlanChange::query()
            ->with([
                'subscription',
                'fromPlan',
                'toPlan',
            ])
            ->find($id);
    }

    public function create(array $data): PlanChange
    {
        return PlanChange::query()->create($data);
    }

    public function update(
        PlanChange $planChange,
        array $data
    ): PlanChange {
        $planChange->update($data);

        return $planChange->fresh([
            'subscription',
            'fromPlan',
            'toPlan',
        ]);
    }

    public function delete(PlanChange $planChange): bool
    {
        return (bool) $planChange->delete();
    }

    public function getBySubscriptionId(
        int $subscriptionId
    ): Collection {
        return PlanChange::query()
            ->with([
                'subscription',
                'fromPlan',
                'toPlan',
            ])
            ->where(
                'subscription_id',
                $subscriptionId
            )
            ->orderByDesc('effective_at')
            ->get();
    }
}