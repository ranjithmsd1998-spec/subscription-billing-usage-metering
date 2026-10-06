<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\PlanChange;
use App\Models\Subscription;
use App\Models\SubscriptionPeriod;
use App\Repositories\PlanChangeRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlanChangeService implements PlanChangeServiceInterface
{
    public function __construct(
        private PlanChangeRepositoryInterface $repository
    ) {
    }

    /**
     * Get all plan changes.
     */
    public function getAll(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    /**
     * Find plan change.
     */
    public function findById(int $id): PlanChange
    {
        $planChange = $this->repository->findById($id);

        if (!$planChange) {
            throw ValidationException::withMessages([
                'plan_change_id' => [
                    'The selected plan change does not exist.',
                ],
            ]);
        }

        return $planChange;
    }

    /**
     * Create plan change.
     *
     * This also splits the current subscription period.
     *
     * Example:
     *
     * 01 Oct -> 06 Oct = Basic
     * 06 Oct -> 01 Nov = Premium
     */
    public function create(array $data): PlanChange
    {
        return DB::transaction(function () use ($data) {

            /*
             * ---------------------------------------------------------
             * 1. Load subscription
             * ---------------------------------------------------------
             */
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

            /*
             * Only active subscriptions can change plans.
             */
            if ($subscription->status !== 'active') {
                throw ValidationException::withMessages([
                    'subscription_id' => [
                        'Only active subscriptions can have a plan change.',
                    ],
                ]);
            }

            /*
             * ---------------------------------------------------------
             * 2. Load current plan
             * ---------------------------------------------------------
             */
            $fromPlanId = $data['from_plan_id']
                ?? $subscription->plan_id;

            $fromPlan = Plan::query()
                ->find($fromPlanId);

            if (!$fromPlan) {
                throw ValidationException::withMessages([
                    'from_plan_id' => [
                        'The selected current plan does not exist.',
                    ],
                ]);
            }

            /*
             * The supplied current plan must match subscription.
             */
            if ((int) $fromPlan->id !== (int) $subscription->plan_id) {
                throw ValidationException::withMessages([
                    'from_plan_id' => [
                        'The current plan does not match the subscription plan.',
                    ],
                ]);
            }

            /*
             * ---------------------------------------------------------
             * 3. Load destination plan
             * ---------------------------------------------------------
             */
            $toPlan = Plan::query()
                ->find($data['to_plan_id']);

            if (!$toPlan) {
                throw ValidationException::withMessages([
                    'to_plan_id' => [
                        'The selected new plan does not exist.',
                    ],
                ]);
            }

            /*
             * Same plan is not allowed.
             */
            if ((int) $fromPlan->id === (int) $toPlan->id) {
                throw ValidationException::withMessages([
                    'to_plan_id' => [
                        'The new plan must be different from the current plan.',
                    ],
                ]);
            }

            /*
             * ---------------------------------------------------------
             * 4. Merchant validation
             * ---------------------------------------------------------
             */
            if (
                (int) $fromPlan->merchant_id !==
                (int) $subscription->merchant_id
            ) {
                throw ValidationException::withMessages([
                    'from_plan_id' => [
                        'The current plan does not belong to the subscription merchant.',
                    ],
                ]);
            }

            if (
                (int) $toPlan->merchant_id !==
                (int) $subscription->merchant_id
            ) {
                throw ValidationException::withMessages([
                    'to_plan_id' => [
                        'The new plan does not belong to the subscription merchant.',
                    ],
                ]);
            }

            /*
             * Destination plan must be active.
             */
            if ($toPlan->status !== 'active') {
                throw ValidationException::withMessages([
                    'to_plan_id' => [
                        'The new plan must be active.',
                    ],
                ]);
            }

            /*
             * ---------------------------------------------------------
             * 5. Determine upgrade / downgrade
             * ---------------------------------------------------------
             */
            $fromPrice = (float) $fromPlan->base_price;
            $toPrice = (float) $toPlan->base_price;

            if ($toPrice > $fromPrice) {
                $changeType = 'upgrade';
            } elseif ($toPrice < $fromPrice) {
                $changeType = 'downgrade';
            } else {
                throw ValidationException::withMessages([
                    'to_plan_id' => [
                        'The new plan must have a different base price.',
                    ],
                ]);
            }

            /*
             * If frontend sends change_type, verify it.
             */
            if (
                isset($data['change_type']) &&
                $data['change_type'] !== $changeType
            ) {
                throw ValidationException::withMessages([
                    'change_type' => [
                        'The change type does not match the plan prices.',
                    ],
                ]);
            }

            /*
             * ---------------------------------------------------------
             * 6. Effective date
             * ---------------------------------------------------------
             */
            $effectiveAt = Carbon::parse(
                $data['effective_at']
            );

            $subscriptionStart = Carbon::parse(
                $subscription->started_at
            );

            if ($effectiveAt->lt($subscriptionStart)) {
                throw ValidationException::withMessages([
                    'effective_at' => [
                        'The effective date cannot be before the subscription start date.',
                    ],
                ]);
            }

            /*
             * Cannot change after cancellation.
             */
            if (
                $subscription->cancelled_at &&
                $effectiveAt->gt(
                    Carbon::parse($subscription->cancelled_at)
                )
            ) {
                throw ValidationException::withMessages([
                    'effective_at' => [
                        'The effective date cannot be after the subscription cancellation date.',
                    ],
                ]);
            }

            /*
             * ---------------------------------------------------------
             * 7. Find current subscription period
             * ---------------------------------------------------------
             */
            $currentPeriod = SubscriptionPeriod::query()
                ->where(
                    'subscription_id',
                    $subscription->id
                )
                ->where(
                    'starts_at',
                    '<=',
                    $effectiveAt
                )
                ->where(function ($query) use ($effectiveAt) {

                    $query
                        ->whereNull('ends_at')
                        ->orWhere(
                            'ends_at',
                            '>',
                            $effectiveAt
                        );

                })
                ->orderByDesc('starts_at')
                ->first();

            if (!$currentPeriod) {
                throw ValidationException::withMessages([
                    'effective_at' => [
                        'No active subscription period was found for the selected effective date.',
                    ],
                ]);
            }

            /*
             * Effective date must be inside the current period.
             */
            $periodStart = Carbon::parse(
                $currentPeriod->starts_at
            );

            $periodEnd = $currentPeriod->ends_at
                ? Carbon::parse($currentPeriod->ends_at)
                : null;

            if ($effectiveAt->lte($periodStart)) {
                throw ValidationException::withMessages([
                    'effective_at' => [
                        'The effective date must be after the current period start date.',
                    ],
                ]);
            }

            if (
                $periodEnd &&
                $effectiveAt->gte($periodEnd)
            ) {
                throw ValidationException::withMessages([
                    'effective_at' => [
                        'The effective date must be before the current period end date.',
                    ],
                ]);
            }

            /*
             * ---------------------------------------------------------
             * 8. Prevent duplicate plan change
             * ---------------------------------------------------------
             */
            $duplicate = PlanChange::query()
                ->where(
                    'subscription_id',
                    $subscription->id
                )
                ->where(
                    'effective_at',
                    $effectiveAt
                )
                ->exists();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'effective_at' => [
                        'A plan change already exists for this subscription at this effective time.',
                    ],
                ]);
            }

            /*
             * ---------------------------------------------------------
             * 9. Save plan change history
             * ---------------------------------------------------------
             */
            $planChange = $this->repository->create([
                'subscription_id' => $subscription->id,
                'from_plan_id' => $fromPlan->id,
                'to_plan_id' => $toPlan->id,
                'effective_at' => $effectiveAt,
                'change_type' => $changeType,
                'reason' => $data['reason'] ?? null,
                'metadata' => null,
            ]);

            /*
             * ---------------------------------------------------------
             * 10. Close OLD subscription period
             * ---------------------------------------------------------
             *
             * Before:
             *
             * 01 Oct -> 01 Nov
             *
             * After:
             *
             * 01 Oct -> 06 Oct
             *
             */
            $oldPeriodEnd = $currentPeriod->ends_at;

            $currentPeriod->update([
                'ends_at' => $effectiveAt,
            ]);

            /*
             * ---------------------------------------------------------
             * 11. Create NEW subscription period
             * ---------------------------------------------------------
             *
             * 06 Oct -> 01 Nov
             *
             * Pricing is copied from NEW PLAN.
             */
            $newPeriod = SubscriptionPeriod::query()->create([
                'subscription_id' => $subscription->id,
                'plan_id' => $toPlan->id,
                'starts_at' => $effectiveAt,
                'ends_at' => $oldPeriodEnd,
                'base_price' => $toPlan->base_price,
                'included_units' => $toPlan->included_units,
                'overage_rate' => $toPlan->overage_rate,
                'billing_cycle' => $toPlan->billing_cycle,
            ]);

            /*
             * ---------------------------------------------------------
             * 12. Store billing split information
             * ---------------------------------------------------------
             */
            $planChange->update([
                'metadata' => [
                    'old_subscription_period_id' => $currentPeriod->id,
                    'new_subscription_period_id' => $newPeriod->id,
                    'from_plan_price' => $fromPrice,
                    'to_plan_price' => $toPrice,
                ],
            ]);

            /*
             * ---------------------------------------------------------
             * 13. Update subscription current plan
             * ---------------------------------------------------------
             *
             * If effective immediately/past:
             *
             * subscription.plan_id = new plan
             */
            // if ($effectiveAt->lte(now())) {

                $subscription->update([
                    'plan_id' => $toPlan->id,
                ]);
            // }

            /*
             * Return fresh record.
             */
            return $this->findById(
                $planChange->id
            );
        });
    }

    /**
     * Update plan change.
     *
     * Billing structure fields are immutable.
     * Only reason and metadata can be changed.
     */
    public function update(
        int $id,
        array $data
    ): PlanChange {

        $planChange = $this->findById($id);

        $allowedData = [];

        if (array_key_exists('reason', $data)) {
            $allowedData['reason'] = $data['reason'];
        }

        if (array_key_exists('metadata', $data)) {
            $allowedData['metadata'] = $data['metadata'];
        }

        if (empty($allowedData)) {
            return $planChange;
        }

        $this->repository->update(
            $planChange,
            $allowedData
        );

        return $this->findById($id);
    }

    /**
     * Delete plan change.
     *
     * Applied billing changes cannot be deleted.
     */
    public function delete(int $id): void
    {
        $planChange = $this->findById($id);

        $metadata = is_array($planChange->metadata)
            ? $planChange->metadata
            : [];

        if (
            !empty($metadata['old_subscription_period_id']) ||
            !empty($metadata['new_subscription_period_id'])
        ) {
            throw ValidationException::withMessages([
                'plan_change_id' => [
                    'Applied plan changes cannot be deleted because they affect billing history.',
                ],
            ]);
        }

        $this->repository->delete(
            $planChange
        );
    }

    /**
     * Get history for subscription.
     */
    public function getBySubscriptionId(
        int $subscriptionId
    ): Collection {
        return $this->repository
            ->getBySubscriptionId(
                $subscriptionId
            );
    }
}