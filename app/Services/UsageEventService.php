<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Merchant;
use App\Models\Subscription;
use App\Models\SubscriptionPeriod;
use App\Models\UsageEvent;
use App\Repositories\UsageEventRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class UsageEventService implements UsageEventServiceInterface
{
    public function __construct(
        private readonly UsageEventRepositoryInterface $repository
    ) {
    }

    public function getAll(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->repository->getAll(
            $filters,
            $perPage
        );
    }

    public function getById(int $id): UsageEvent
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): UsageEvent
    {
        $merchant = Merchant::query()
            ->findOrFail($data['merchant_id']);

        $customer = Customer::query()
            ->findOrFail($data['customer_id']);

        $subscription = Subscription::query()
            ->with('plan')
            ->findOrFail($data['subscription_id']);

        $period = SubscriptionPeriod::query()
            ->with('plan')
            ->findOrFail($data['subscription_period_id']);

        $this->validateRelationships(
            $merchant,
            $customer,
            $subscription,
            $period
        );

        $this->validateSubscriptionStatus(
            $subscription
        );

        $occurredAt = Carbon::parse(
            $data['occurred_at']
        );

        $this->validateOccurredAt(
            $period,
            $occurredAt
        );

        $existingEvent = $this->repository
            ->findByIdempotencyKey(
                $merchant->id,
                $data['idempotency_key']
            );

        if ($existingEvent) {
            throw ValidationException::withMessages([
                'idempotency_key' => [
                    'A usage event with this idempotency key already exists for this merchant.',
                ],
            ]);
        }

        /*
         * Store the actual related IDs after validating that
         * all relationships belong to the same billing context.
         */
        $data['merchant_id'] = $merchant->id;
        $data['customer_id'] = $customer->id;
        $data['subscription_id'] = $subscription->id;
        $data['subscription_period_id'] = $period->id;

        return $this->repository->create($data);
    }

    public function update(
        int $id,
        array $data
    ): UsageEvent {
        $usageEvent = $this->repository->findById($id);

        /*
         * Identity fields are immutable.
         *
         * merchant_id
         * customer_id
         * subscription_id
         * subscription_period_id
         * idempotency_key
         *
         * These are intentionally not accepted during update.
         */

        $occurredAt = isset($data['occurred_at'])
            ? Carbon::parse($data['occurred_at'])
            : $usageEvent->occurred_at;

        $period = $usageEvent->subscriptionPeriod;

        $this->validateOccurredAt(
            $period,
            $occurredAt
        );

        $updateData = [];

        if (array_key_exists('usage_units', $data)) {
            $updateData['usage_units'] = $data['usage_units'];
        }

        if (array_key_exists('unit_name', $data)) {
            $updateData['unit_name'] = $data['unit_name'];
        }

        if (array_key_exists('occurred_at', $data)) {
            $updateData['occurred_at'] = $occurredAt;
        }

        if (array_key_exists('metadata', $data)) {
            $updateData['metadata'] = $data['metadata'];
        }

        return $this->repository->update(
            $usageEvent,
            $updateData
        );
    }

    public function delete(int $id): void
    {
        $usageEvent = $this->repository->findById($id);

        /*
         * Usage events are billing records.
         * They should not be casually deleted because they can
         * affect usage calculations and future invoices.
         */
        throw ValidationException::withMessages([
            'usage_event' => [
                'Usage events cannot be deleted because they are billing records.',
            ],
        ]);
    }

    private function validateRelationships(
        Merchant $merchant,
        Customer $customer,
        Subscription $subscription,
        SubscriptionPeriod $period
    ): void {
        if ((int) $customer->merchant_id !== (int) $merchant->id) {
            throw ValidationException::withMessages([
                'customer_id' => [
                    'The customer does not belong to the selected merchant.',
                ],
            ]);
        }

        if ((int) $subscription->merchant_id !== (int) $merchant->id) {
            throw ValidationException::withMessages([
                'subscription_id' => [
                    'The subscription does not belong to the selected merchant.',
                ],
            ]);
        }

        if ((int) $subscription->customer_id !== (int) $customer->id) {
            throw ValidationException::withMessages([
                'subscription_id' => [
                    'The subscription does not belong to the selected customer.',
                ],
            ]);
        }

        if ((int) $period->subscription_id !== (int) $subscription->id) {
            throw ValidationException::withMessages([
                'subscription_period_id' => [
                    'The subscription period does not belong to the selected subscription.',
                ],
            ]);
        }

        if ((int) $period->plan_id !== (int) $subscription->plan_id) {
            throw ValidationException::withMessages([
                'subscription_period_id' => [
                    'The subscription period plan does not match the subscription plan.',
                ],
            ]);
        }
    }

    private function validateSubscriptionStatus(
        Subscription $subscription
    ): void {
        if ($subscription->status !== 'active') {
            throw ValidationException::withMessages([
                'subscription_id' => [
                    'Usage can only be recorded for an active subscription.',
                ],
            ]);
        }
    }

    private function validateOccurredAt(
        SubscriptionPeriod $period,
        Carbon $occurredAt
    ): void {
        $startsAt = Carbon::parse(
            $period->starts_at
        );

        if ($occurredAt->lessThan($startsAt)) {
            throw ValidationException::withMessages([
                'occurred_at' => [
                    'The usage event cannot occur before the subscription period starts.',
                ],
            ]);
        }

        if (
            $period->ends_at !== null &&
            $occurredAt->greaterThan(
                Carbon::parse($period->ends_at)
            )
        ) {
            throw ValidationException::withMessages([
                'occurred_at' => [
                    'The usage event cannot occur after the subscription period ends.',
                ],
            ]);
        }
    }
}