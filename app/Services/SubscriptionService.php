<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Plan;
use App\Models\Subscription;
use App\Repositories\SubscriptionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class SubscriptionService implements SubscriptionServiceInterface
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptionRepository
    ) {
    }

    /**
     * Get all subscriptions.
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return $this->subscriptionRepository->getAll($filters);
    }

    /**
     * Get subscription by ID.
     */
    public function getById(int $id): Subscription
    {
        $subscription = $this->subscriptionRepository->findById($id);

        if (!$subscription) {
            throw (new ModelNotFoundException)
                ->setModel(Subscription::class, [$id]);
        }

        return $subscription;
    }

    public function findById(int $id): ?Subscription
    {
        return $this->subscriptionRepository->findById($id);
    }

    /**
     * Create subscription.
     */
    public function create(array $data): Subscription
    {
        $this->validateBusinessRules($data);

        $data['status'] = $data['status'] ?? 'active';

        return $this->subscriptionRepository->create($data);
    }

    /**
     * Update subscription.
     */
    public function update(int $id, array $data): Subscription
    {
        $subscription = $this->getById($id);

        $mergedData = array_merge(
            $subscription->only([
                'merchant_id',
                'customer_id',
                'plan_id',
                'status',
                'started_at',
                'current_period_start',
                'current_period_end',
                'cancelled_at',
            ]),
            $data
        );

        $this->validateBusinessRules(
            $mergedData,
            $subscription->id
        );

        /*
         * If subscription is cancelled, automatically set cancelled_at.
         */
        if (
            ($data['status'] ?? $subscription->status) === 'cancelled'
            && empty($data['cancelled_at'])
            && empty($subscription->cancelled_at)
        ) {
            $data['cancelled_at'] = now();
        }

        /*
         * If subscription is changed from cancelled to another status,
         * clear cancelled_at.
         */
        if (
            isset($data['status'])
            && $data['status'] !== 'cancelled'
        ) {
            $data['cancelled_at'] = null;
        }

        return $this->subscriptionRepository->update(
            $subscription,
            $data
        );
    }

    /**
     * Delete subscription.
     */
    public function delete(int $id): bool
    {
        $subscription = $this->getById($id);

        /*
         * Active subscriptions must be cancelled first.
         */
        if ($subscription->isActive()) {
            throw ValidationException::withMessages([
                'subscription' => [
                    'Active subscriptions cannot be deleted. Cancel the subscription first.',
                ],
            ]);
        }

        return $this->subscriptionRepository->delete($subscription);
    }

    /**
     * Validate subscription business rules.
     */
    private function validateBusinessRules(
        array $data,
        ?int $ignoreSubscriptionId = null
    ): void {
        /*
         * Customer and plan must belong to the same merchant.
         */
        $customerMerchantId = Customer::query()
            ->whereKey($data['customer_id'])
            ->value('merchant_id');

        $planMerchantId = Plan::query()
            ->whereKey($data['plan_id'])
            ->value('merchant_id');

        if (
            $customerMerchantId === null
            || $planMerchantId === null
            || (int) $customerMerchantId !== (int) $planMerchantId
            || (int) $customerMerchantId !== (int) $data['merchant_id']
        ) {
            throw ValidationException::withMessages([
                'merchant_id' => [
                    'Merchant, customer, and plan must belong to the same merchant.',
                ],
            ]);
        }

        /*
         * Plan must be active.
         */
        $planStatus = Plan::query()
            ->whereKey($data['plan_id'])
            ->value('status');

        if ($planStatus !== 'active') {
            throw ValidationException::withMessages([
                'plan_id' => [
                    'The selected plan is not active.',
                ],
            ]);
        }

        /*
         * Only one active subscription for the same customer and plan.
         */
        $status = $data['status'] ?? 'active';

        if (
            $status === 'active'
            && $this->subscriptionRepository->hasActiveSubscription(
                (int) $data['customer_id'],
                (int) $data['plan_id'],
                $ignoreSubscriptionId
            )
        ) {
            throw ValidationException::withMessages([
                'plan_id' => [
                    'The customer already has an active subscription for this plan.',
                ],
            ]);
        }

        /*
         * Current period end cannot be before current period start.
         */
        if (
            !empty($data['current_period_start'])
            && !empty($data['current_period_end'])
            && strtotime($data['current_period_end'])
                < strtotime($data['current_period_start'])
        ) {
            throw ValidationException::withMessages([
                'current_period_end' => [
                    'The current period end must be after or equal to the current period start.',
                ],
            ]);
        }

        /*
         * Started date should not be after current period start.
         */
        if (
            !empty($data['started_at'])
            && !empty($data['current_period_start'])
            && strtotime($data['started_at'])
                > strtotime($data['current_period_start'])
        ) {
            throw ValidationException::withMessages([
                'current_period_start' => [
                    'The current period start must be on or after the subscription start date.',
                ],
            ]);
        }

        /*
         * Cancelled subscriptions should have cancelled_at.
         */
        if (
            $status === 'cancelled'
            && empty($data['cancelled_at'])
            && $ignoreSubscriptionId === null
        ) {
            $data['cancelled_at'] = now();
        }
    }
}