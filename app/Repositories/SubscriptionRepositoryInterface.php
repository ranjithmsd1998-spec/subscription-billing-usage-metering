<?php

namespace App\Repositories;

use App\Models\Subscription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SubscriptionRepositoryInterface
{
    public function getAll(array $filters = []): LengthAwarePaginator;

    public function findById(int $id): ?Subscription;

    public function create(array $data): Subscription;

    public function update(
        Subscription $subscription,
        array $data
    ): Subscription;

    public function delete(Subscription $subscription): bool;

    public function hasActiveSubscription(
        int $customerId,
        int $planId,
        ?int $ignoreSubscriptionId = null
    ): bool;
}