<?php

namespace App\Services;

use App\Models\DailyUsageAggregate;
use App\Models\SubscriptionPeriod;
use App\Repositories\DailyUsageAggregateRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DailyUsageAggregateService implements DailyUsageAggregateServiceInterface
{
    public function __construct(
        private DailyUsageAggregateRepositoryInterface $repository
    ) {
    }

    public function generate(
        int $subscriptionPeriodId,
        string $usageDate
    ): DailyUsageAggregate {
        return DB::transaction(function () use (
            $subscriptionPeriodId,
            $usageDate
        ) {
            $period = SubscriptionPeriod::query()
                ->with([
                    'subscription',
                    'subscription.merchant',
                    'subscription.customer',
                    'subscription.plan',
                ])
                ->find($subscriptionPeriodId);

            if (!$period) {
                throw ValidationException::withMessages([
                    'subscription_period_id' => [
                        'The selected subscription period does not exist.',
                    ],
                ]);
            }

            $date = Carbon::parse($usageDate)->startOfDay();

            /*
             * The aggregate date must belong to the subscription period.
             */
            if (!$period->containsDate($date)) {
                throw ValidationException::withMessages([
                    'usage_date' => [
                        'The usage date must be within the subscription period.',
                    ],
                ]);
            }

            $subscription = $period->subscription;

            if (!$subscription) {
                throw ValidationException::withMessages([
                    'subscription_period_id' => [
                        'The subscription period is not linked to a valid subscription.',
                    ],
                ]);
            }

            /*
             * Get all usage events for this subscription period
             * on the requested date.
             */
            $usageEvents = $subscription->usageEvents()
                ->where('subscription_period_id', $period->id)
                ->whereDate('occurred_at', $date->toDateString())
                ->get();

            $totalUnits = (int) $usageEvents->sum('usage_units');
            $eventCount = $usageEvents->count();

            $data = [
                'merchant_id' => $subscription->merchant_id,
                'customer_id' => $subscription->customer_id,
                'subscription_id' => $subscription->id,
                'subscription_period_id' => $period->id,
                'usage_date' => $date->toDateString(),
                'total_usage_units' => $totalUnits,
                'event_count' => $eventCount,
            ];

            $existingAggregate = $this->repository
                ->findBySubscriptionPeriodAndDate(
                    $period->id,
                    $date
                );

            if ($existingAggregate) {
                return $this->repository->update(
                    $existingAggregate,
                    [
                        'total_usage_units' => $totalUnits,
                        'event_count' => $eventCount,
                    ]
                );
            }

            return $this->repository->create($data);
        });
    }

    public function findById(int $id): DailyUsageAggregate
    {
        $aggregate = $this->repository->findById($id);

        if (!$aggregate) {
            throw ValidationException::withMessages([
                'id' => [
                    'Daily usage aggregate not found.',
                ],
            ]);
        }

        return $aggregate;
    }

    public function getBySubscriptionPeriod(
        int $subscriptionPeriodId
    ): Collection {
        return $this->repository->getBySubscriptionPeriod(
            $subscriptionPeriodId
        );
    }
}