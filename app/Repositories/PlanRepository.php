<?php

namespace App\Repositories;

use App\Models\Plan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PlanRepository implements PlanRepositoryInterface
{
    /**
     * Get all plans.
     */
    public function getAll(): Collection
    {
        return Plan::query()
            ->latest()
            ->get();
    }

    /**
     * Get paginated plans.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Plan::query()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find a plan by ID.
     */
    public function findById(int $id): ?Plan
    {
        return Plan::find($id);
    }

    /**
     * Create a new plan.
     */
    public function create(array $data): Plan
    {
        return Plan::create($data);
    }

    /**
     * Update an existing plan.
     */
    public function update(
        Plan $plan,
        array $data
    ): Plan {
        $plan->update($data);

        return $plan->refresh();
    }

    /**
     * Delete a plan.
     */
    public function delete(Plan $plan): bool
    {
        return (bool) $plan->delete();
    }

    /**
     * Check whether a plan name already exists
     * for the same merchant.
     */
    public function existsByName(
        int $merchantId,
        string $name,
        ?int $ignoreId = null
    ): bool {
        return Plan::query()
            ->where('merchant_id', $merchantId)
            ->where('name', $name)
            ->when(
                $ignoreId !== null,
                fn ($query) => $query->where(
                    'id',
                    '!=',
                    $ignoreId
                )
            )
            ->exists();
    }

    /**
     * Check whether a plan code already exists
     * for the same merchant.
     */
    public function existsByCode(
        int $merchantId,
        string $code,
        ?int $ignoreId = null
    ): bool {
        return Plan::query()
            ->where('merchant_id', $merchantId)
            ->where('code', $code)
            ->when(
                $ignoreId !== null,
                fn ($query) => $query->where(
                    'id',
                    '!=',
                    $ignoreId
                )
            )
            ->exists();
    }
}