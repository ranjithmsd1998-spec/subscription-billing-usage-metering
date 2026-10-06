<?php

namespace App\Repositories;

use App\Models\UsageEvent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UsageEventRepository implements UsageEventRepositoryInterface
{
    public function getAll(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = UsageEvent::query()
            ->with([
                'merchant',
                'customer',
                'subscription',
                'subscriptionPeriod',
            ])
            ->latest('occurred_at');

        if (!empty($filters['merchant_id'])) {
            $query->where(
                'merchant_id',
                $filters['merchant_id']
            );
        }

        if (!empty($filters['customer_id'])) {
            $query->where(
                'customer_id',
                $filters['customer_id']
            );
        }

        if (!empty($filters['subscription_id'])) {
            $query->where(
                'subscription_id',
                $filters['subscription_id']
            );
        }

        if (!empty($filters['subscription_period_id'])) {
            $query->where(
                'subscription_period_id',
                $filters['subscription_period_id']
            );
        }

        if (!empty($filters['unit_name'])) {
            $query->where(
                'unit_name',
                $filters['unit_name']
            );
        }

        if (!empty($filters['occurred_from'])) {
            $query->where(
                'occurred_at',
                '>=',
                $filters['occurred_from']
            );
        }

        if (!empty($filters['occurred_to'])) {
            $query->where(
                'occurred_at',
                '<=',
                $filters['occurred_to']
            );
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): UsageEvent
    {
        return UsageEvent::query()
            ->with([
                'merchant',
                'customer',
                'subscription',
                'subscriptionPeriod',
            ])
            ->findOrFail($id);
    }

    public function create(array $data): UsageEvent
    {
        $usageEvent = UsageEvent::create($data);

        return $usageEvent->load([
            'merchant',
            'customer',
            'subscription',
            'subscriptionPeriod',
        ]);
    }

    public function update(
        UsageEvent $usageEvent,
        array $data
    ): UsageEvent {
        $usageEvent->update($data);

        return $usageEvent->fresh([
            'merchant',
            'customer',
            'subscription',
            'subscriptionPeriod',
        ]);
    }

    public function delete(UsageEvent $usageEvent): void
    {
        $usageEvent->delete();
    }

    public function findByIdempotencyKey(
        int $merchantId,
        string $idempotencyKey
    ): ?UsageEvent {
        return UsageEvent::query()
            ->where('merchant_id', $merchantId)
            ->where('idempotency_key', $idempotencyKey)
            ->first();
    }
}