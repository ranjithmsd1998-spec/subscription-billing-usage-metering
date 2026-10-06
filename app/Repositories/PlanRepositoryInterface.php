<?php

namespace App\Repositories;

use App\Models\Plan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PlanRepositoryInterface
{
    /**
     * Get all plans.
     */
    public function getAll(): Collection;

    /**
     * Get paginated plans.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a plan by ID.
     */
    public function findById(int $id): ?Plan;

    /**
     * Create a new plan.
     */
    public function create(array $data): Plan;

    /**
     * Update an existing plan.
     */
    public function update(Plan $plan, array $data): Plan;

    /**
     * Delete a plan.
     */
    public function delete(Plan $plan): bool;

    /**
     * Check whether a plan name already exists
     * for the same merchant.
     */
    public function existsByName(
        int $merchantId,
        string $name,
        ?int $ignoreId = null
    ): bool;

    /**
     * Check whether a plan code already exists
     * for the same merchant.
     */
    public function existsByCode(
        int $merchantId,
        string $code,
        ?int $ignoreId = null
    ): bool;
}