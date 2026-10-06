<?php

namespace App\Repositories;

use App\Models\SubscriptionPeriod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SubscriptionPeriodRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): SubscriptionPeriod;

    public function create(array $data): SubscriptionPeriod;

    public function update(SubscriptionPeriod $period, array $data): SubscriptionPeriod;

    public function delete(SubscriptionPeriod $period): void;

    public function hasOverlappingPeriod(
        int $subscriptionId,
        string $startsAt,
        ?string $endsAt,
        ?int $excludeId = null
    ): bool;
}