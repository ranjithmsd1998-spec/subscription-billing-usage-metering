<?php

namespace App\Services;

use App\Models\DailyUsageAggregate;
use Illuminate\Database\Eloquent\Collection;

interface DailyUsageAggregateServiceInterface
{
    public function generate(
        int $subscriptionPeriodId,
        string $usageDate
    ): DailyUsageAggregate;

    public function findById(int $id): DailyUsageAggregate;

    public function getBySubscriptionPeriod(
        int $subscriptionPeriodId
    ): Collection;
}