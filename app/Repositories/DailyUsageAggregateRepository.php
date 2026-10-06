<?php

namespace App\Repositories;

use App\Models\DailyUsageAggregate;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class DailyUsageAggregateRepository implements DailyUsageAggregateRepositoryInterface
{
    public function findBySubscriptionPeriodAndDate(
        int $subscriptionPeriodId,
        Carbon $usageDate
    ): ?DailyUsageAggregate {
        return DailyUsageAggregate::query()
            ->where('subscription_period_id', $subscriptionPeriodId)
            ->whereDate('usage_date', $usageDate->toDateString())
            ->first();
    }

    public function create(array $data): DailyUsageAggregate
    {
        return DailyUsageAggregate::create($data);
    }

    public function update(
        DailyUsageAggregate $aggregate,
        array $data
    ): DailyUsageAggregate {
        $aggregate->update($data);

        return $aggregate->fresh();
    }

    public function findById(int $id): ?DailyUsageAggregate
    {
        return DailyUsageAggregate::query()
            ->with([
                'merchant',
                'customer',
                'subscription',
                'subscriptionPeriod',
            ])
            ->find($id);
    }

    public function getBySubscriptionPeriod(
        int $subscriptionPeriodId
    ): Collection {
        return DailyUsageAggregate::query()
            ->with([
                'merchant',
                'customer',
                'subscription',
                'subscriptionPeriod',
            ])
            ->where('subscription_period_id', $subscriptionPeriodId)
            ->orderBy('usage_date')
            ->get();
    }
}