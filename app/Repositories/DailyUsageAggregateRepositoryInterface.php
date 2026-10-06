<?php

namespace App\Repositories;

use App\Models\DailyUsageAggregate;
use App\Models\SubscriptionPeriod;
use Carbon\Carbon;

interface DailyUsageAggregateRepositoryInterface
{
    public function findBySubscriptionPeriodAndDate(
        int $subscriptionPeriodId,
        Carbon $usageDate
    ): ?DailyUsageAggregate;

    public function create(array $data): DailyUsageAggregate;

    public function update(
        DailyUsageAggregate $aggregate,
        array $data
    ): DailyUsageAggregate;

    public function findById(int $id): ?DailyUsageAggregate;

    public function getBySubscriptionPeriod(
        int $subscriptionPeriodId
    ): \Illuminate\Database\Eloquent\Collection;
}