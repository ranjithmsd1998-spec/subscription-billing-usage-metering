<?php

namespace App\Services;

use App\Models\PlanChange;
use Illuminate\Database\Eloquent\Collection;

interface PlanChangeServiceInterface
{
    public function getAll(array $filters = []): Collection;

    public function findById(int $id): PlanChange;

    public function create(array $data): PlanChange;

    public function update(int $id, array $data): PlanChange;

    public function delete(int $id): void;

    public function getBySubscriptionId(
        int $subscriptionId
    ): Collection;
}