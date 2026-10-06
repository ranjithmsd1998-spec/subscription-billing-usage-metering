<?php

namespace App\Repositories;

use App\Models\PlanChange;
use Illuminate\Database\Eloquent\Collection;

interface PlanChangeRepositoryInterface
{
    public function getAll(array $filters = []): Collection;

    public function findById(int $id): ?PlanChange;

    public function create(array $data): PlanChange;

    public function update(
        PlanChange $planChange,
        array $data
    ): PlanChange;

    public function delete(PlanChange $planChange): bool;

    public function getBySubscriptionId(
        int $subscriptionId
    ): Collection;
}