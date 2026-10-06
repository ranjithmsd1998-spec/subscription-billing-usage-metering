<?php

namespace App\Services;

use App\Models\Plan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PlanServiceInterface
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
    public function findById(int $id): Plan;

    /**
     * Create a new plan.
     */
    public function create(array $data): Plan;

    /**
     * Update an existing plan.
     */
    public function update(int $id, array $data): Plan;

    /**
     * Delete a plan.
     */
    public function delete(int $id): bool;
}