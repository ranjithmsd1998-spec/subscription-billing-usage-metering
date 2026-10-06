<?php

namespace App\Repositories;

use App\Models\SubscriptionPeriod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SubscriptionPeriodRepository implements SubscriptionPeriodRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SubscriptionPeriod::query()
            ->with([
                'subscription',
                'plan',
            ])
            ->latest('starts_at');

        if (!empty($filters['subscription_id'])) {
            $query->where('subscription_id', $filters['subscription_id']);
        }

        if (!empty($filters['plan_id'])) {
            $query->where('plan_id', $filters['plan_id']);
        }

        if (!empty($filters['billing_cycle'])) {
            $query->where('billing_cycle', $filters['billing_cycle']);
        }

        if (!empty($filters['starts_from'])) {
            $query->whereDate('starts_at', '>=', $filters['starts_from']);
        }

        if (!empty($filters['starts_to'])) {
            $query->whereDate('starts_at', '<=', $filters['starts_to']);
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): SubscriptionPeriod
    {
        return SubscriptionPeriod::query()
            ->with([
                'subscription',
                'plan',
            ])
            ->findOrFail($id);
    }

    public function create(array $data): SubscriptionPeriod
    {
        $period = SubscriptionPeriod::create($data);

        return $period->load([
            'subscription',
            'plan',
        ]);
    }

    public function update(
        SubscriptionPeriod $period,
        array $data
    ): SubscriptionPeriod {
        $period->update($data);

        return $period->fresh([
            'subscription',
            'plan',
        ]);
    }

    public function delete(SubscriptionPeriod $period): void
    {
        $period->delete();
    }

    public function hasOverlappingPeriod(
        int $subscriptionId,
        string $startsAt,
        ?string $endsAt,
        ?int $excludeId = null
    ): bool {
        $query = SubscriptionPeriod::query()
            ->where('subscription_id', $subscriptionId);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        /*
         * Overlap logic:
         *
         * Existing period:
         * existing starts < new ends
         * AND
         * existing ends is null OR existing ends > new starts
         *
         * For an open-ended new period, any existing period that
         * ends after the new start is considered overlapping.
         */
        $query->where('starts_at', '<', $endsAt ?? '9999-12-31 23:59:59')
            ->where(function ($q) use ($startsAt) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>', $startsAt);
            });

        return $query->exists();
    }
}