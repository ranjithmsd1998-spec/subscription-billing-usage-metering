<?php

namespace App\Repositories;

use App\Models\Subscription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    /**
     * Get paginated subscriptions.
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = Subscription::query()
            ->with([
                'merchant',
                'customer',
                'plan',
            ]);

        if (!empty($filters['merchant_id'])) {
            $query->where('merchant_id', $filters['merchant_id']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['plan_id'])) {
            $query->where('plan_id', $filters['plan_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query
            ->latest('id')
            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Find subscription by ID.
     */
    public function findById(int $id): ?Subscription
    {
        return Subscription::query()
            ->with([
                'merchant',
                'customer',
                'plan',
            ])
            ->find($id);
    }

    /**
     * Create subscription.
     */
    public function create(array $data): Subscription
    {
        $subscription = Subscription::create($data);

        return $subscription->load([
            'merchant',
            'customer',
            'plan',
        ]);
    }

    /**
     * Update subscription.
     */
    public function update(
        Subscription $subscription,
        array $data
    ): Subscription {
        $subscription->update($data);

        return $subscription->refresh()->load([
            'merchant',
            'customer',
            'plan',
        ]);
    }

    /**
     * Delete subscription.
     */
    public function delete(Subscription $subscription): bool
    {
        return (bool) $subscription->delete();
    }

    /**
     * Check whether customer already has an active subscription
     * for the given plan.
     */
    public function hasActiveSubscription(
        int $customerId,
        int $planId,
        ?int $ignoreSubscriptionId = null
    ): bool {
        $query = Subscription::query()
            ->where('customer_id', $customerId)
            ->where('plan_id', $planId)
            ->where('status', 'active');

        if ($ignoreSubscriptionId !== null) {
            $query->where('id', '!=', $ignoreSubscriptionId);
        }

        return $query->exists();
    }
}