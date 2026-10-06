<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionPeriod;
use App\Repositories\SubscriptionPeriodRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class SubscriptionPeriodService implements SubscriptionPeriodServiceInterface
{
    public function __construct(
        private SubscriptionPeriodRepositoryInterface $repository
    ) {
    }

    /**
     * Get subscription periods.
     */
    public function getAll(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->repository->getAll(
            $filters,
            $perPage
        );
    }

    /**
     * Get subscription period by ID.
     */
    public function getById(int $id): SubscriptionPeriod
    {
        $period = $this->repository->findById($id);

        if (!$period) {
            throw ValidationException::withMessages([
                'subscription_period_id' => [
                    'The selected subscription period does not exist.',
                ],
            ]);
        }

        return $period;
    }

    /**
     * Create subscription period.
     *
     * Pricing is always copied from the subscription's
     * current plan so that historical billing remains immutable.
     */
    public function create(array $data): SubscriptionPeriod
    {
        $subscription = Subscription::query()
            ->with([
                'merchant',
                'customer',
                'plan',
            ])
            ->find($data['subscription_id']);

        if (!$subscription) {
            throw ValidationException::withMessages([
                'subscription_id' => [
                    'The selected subscription does not exist.',
                ],
            ]);
        }

        if (!$subscription->plan) {
            throw ValidationException::withMessages([
                'subscription_id' => [
                    'The subscription does not have a valid plan.',
                ],
            ]);
        }

        /*
         * ---------------------------------------------------------
         * Validate dates
         * ---------------------------------------------------------
         */
        $startsAt = Carbon::parse(
            $data['starts_at']
        );

        $endsAt = !empty($data['ends_at'])
            ? Carbon::parse($data['ends_at'])
            : null;

        if (
            $endsAt &&
            $endsAt->lte($startsAt)
        ) {
            throw ValidationException::withMessages([
                'ends_at' => [
                    'The period end date must be after the start date.',
                ],
            ]);
        }

        /*
         * Period cannot start before subscription start.
         */
        $subscriptionStartedAt = Carbon::parse(
            $subscription->started_at
        );

        if ($startsAt->lt($subscriptionStartedAt)) {
            throw ValidationException::withMessages([
                'starts_at' => [
                    'The subscription period cannot start before the subscription started_at date.',
                ],
            ]);
        }

        /*
         * Period cannot start after subscription cancellation.
         */
        if (
            $subscription->cancelled_at &&
            $startsAt->gt(
                Carbon::parse($subscription->cancelled_at)
            )
        ) {
            throw ValidationException::withMessages([
                'starts_at' => [
                    'The subscription period cannot start after the subscription cancellation date.',
                ],
            ]);
        }

        /*
         * ---------------------------------------------------------
         * Prevent overlapping periods
         * ---------------------------------------------------------
         */
        $overlapExists = SubscriptionPeriod::query()
            ->where(
                'subscription_id',
                $subscription->id
            )
            ->where(function ($query) use (
                $startsAt,
                $endsAt
            ) {

                if ($endsAt) {
                    $query
                        ->where(function ($q) use ($startsAt, $endsAt) {
                            $q->where('starts_at', '<', $endsAt)
                                ->where(function ($q2) use ($startsAt) {
                                    $q2->whereNull('ends_at')
                                        ->orWhere('ends_at', '>', $startsAt);
                                });
                        });
                } else {
                    $query
                        ->whereNull('ends_at')
                        ->orWhere('ends_at', '>', $startsAt);
                }
            })
            ->exists();

        if ($overlapExists) {
            throw ValidationException::withMessages([
                'starts_at' => [
                    'The subscription period overlaps with an existing subscription period.',
                ],
            ]);
        }

        /*
         * ---------------------------------------------------------
         * Create pricing snapshot
         * ---------------------------------------------------------
         *
         * IMPORTANT:
         *
         * We copy plan pricing into the period.
         *
         * This means if the plan price changes later,
         * old billing periods remain unchanged.
         */
        $plan = $subscription->plan;

        $periodData = [
            'subscription_id' => $subscription->id,

            'plan_id' => $plan->id,

            'starts_at' => $startsAt,

            'ends_at' => $endsAt,

            'base_price' => $plan->base_price,

            'included_units' => $plan->included_units,

            'overage_rate' => $plan->overage_rate,

            'billing_cycle' => $plan->billing_cycle,
        ];

        return $this->repository->create(
            $periodData
        );
    }

    /**
     * Update subscription period.
     *
     * Pricing and subscription cannot be changed after creation.
     */
    public function update(
        int $id,
        array $data
    ): SubscriptionPeriod {
        $period = $this->getById($id);

        /*
         * Only dates are editable.
         */
        $startsAt = array_key_exists(
            'starts_at',
            $data
        )
            ? Carbon::parse($data['starts_at'])
            : Carbon::parse($period->starts_at);

        $endsAt = array_key_exists(
            'ends_at',
            $data
        )
            ? (
                !empty($data['ends_at'])
                    ? Carbon::parse($data['ends_at'])
                    : null
            )
            : (
                $period->ends_at
                    ? Carbon::parse($period->ends_at)
                    : null
            );

        /*
         * Validate date order.
         */
        if (
            $endsAt &&
            $endsAt->lte($startsAt)
        ) {
            throw ValidationException::withMessages([
                'ends_at' => [
                    'The period end date must be after the start date.',
                ],
            ]);
        }

        /*
         * Validate against subscription start.
         */
        $subscription = $period->subscription;

        if (!$subscription) {
            throw ValidationException::withMessages([
                'subscription_period_id' => [
                    'The subscription period does not have a valid subscription.',
                ],
            ]);
        }

        $subscriptionStartedAt = Carbon::parse(
            $subscription->started_at
        );

        if ($startsAt->lt($subscriptionStartedAt)) {
            throw ValidationException::withMessages([
                'starts_at' => [
                    'The subscription period cannot start before the subscription started_at date.',
                ],
            ]);
        }

        /*
         * Validate against cancellation.
         */
        if (
            $subscription->cancelled_at &&
            $startsAt->gt(
                Carbon::parse($subscription->cancelled_at)
            )
        ) {
            throw ValidationException::withMessages([
                'starts_at' => [
                    'The subscription period cannot start after the subscription cancellation date.',
                ],
            ]);
        }

        /*
         * ---------------------------------------------------------
         * Prevent overlap with other periods
         * ---------------------------------------------------------
         */
        $overlapExists = SubscriptionPeriod::query()
            ->where(
                'subscription_id',
                $period->subscription_id
            )
            ->where(
                'id',
                '!=',
                $period->id
            )
            ->where(function ($query) use (
                $startsAt,
                $endsAt
            ) {

                if ($endsAt) {
                    $query
                        ->where('starts_at', '<', $endsAt)
                        ->where(function ($q) use ($startsAt) {
                            $q->whereNull('ends_at')
                                ->orWhere('ends_at', '>', $startsAt);
                        });
                } else {
                    $query
                        ->whereNull('ends_at')
                        ->orWhere('ends_at', '>', $startsAt);
                }
            })
            ->exists();

        if ($overlapExists) {
            throw ValidationException::withMessages([
                'starts_at' => [
                    'The subscription period overlaps with another subscription period.',
                ],
            ]);
        }

        /*
         * ---------------------------------------------------------
         * Do not allow modification of a billed period
         * ---------------------------------------------------------
         *
         * Once usage or invoice records exist, changing dates could
         * change billing history.
         */
        $hasUsage = $period->usageEvents()->exists();

        $hasInvoice = $period->invoices()->exists();

        if (
            $hasUsage ||
            $hasInvoice
        ) {
            throw ValidationException::withMessages([
                'subscription_period_id' => [
                    'A subscription period with usage or invoice records cannot be modified.',
                ],
            ]);
        }

        return $this->repository->update(
            $period,
            [
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]
        );
    }

    /**
     * Delete subscription period.
     */
    public function delete(int $id): void
    {
        $period = $this->getById($id);

        /*
         * Billing records must never be deleted.
         */
        if (
            $period->usageEvents()->exists()
        ) {
            throw ValidationException::withMessages([
                'subscription_period_id' => [
                    'A subscription period with usage events cannot be deleted.',
                ],
            ]);
        }

        if (
            $period->invoices()->exists()
        ) {
            throw ValidationException::withMessages([
                'subscription_period_id' => [
                    'A subscription period with invoices cannot be deleted.',
                ],
            ]);
        }

        /*
         * Daily aggregates are derived data.
         * They should also prevent deletion unless removed safely.
         */
        if (
            $period->dailyUsageAggregates()->exists()
        ) {
            throw ValidationException::withMessages([
                'subscription_period_id' => [
                    'A subscription period with daily usage aggregates cannot be deleted.',
                ],
            ]);
        }

        $this->repository->delete(
            $period
        );
    }
}