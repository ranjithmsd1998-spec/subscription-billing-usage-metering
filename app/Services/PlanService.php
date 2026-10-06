<?php

namespace App\Services;

use App\Models\Plan;
use App\Repositories\PlanRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class PlanService implements PlanServiceInterface
{
    /**
     * Cache TTL for plan/pricing data.
     *
     * Plan data does not change frequently, so keeping it cached
     * reduces repeated database lookups during usage and billing.
     */
    private const CACHE_TTL = 3600;

    public function __construct(
        protected PlanRepositoryInterface $planRepository
    ) {
    }

    /**
     * Get all plans.
     */
    public function getAll(): Collection
    {
        return $this->planRepository->getAll();
    }

    /**
     * Get paginated plans.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->planRepository->paginate($perPage);
    }

    /**
     * Find a plan by ID.
     *
     * Plan/pricing data is cached to reduce repeated database queries.
     */
    public function findById(int $id): Plan
    {
        $cacheKey = $this->getCacheKey($id);

        $plan = Cache::remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($id) {
                return $this->planRepository->findById($id);
            }
        );

        if (!$plan) {
            throw new ModelNotFoundException(
                'Plan not found.'
            );
        }

        return $plan;
    }

    /**
     * Create a new plan.
     */
    public function create(array $data): Plan
    {
        if (
            $this->planRepository->existsByName(
                $data['merchant_id'],
                $data['name']
            )
        ) {
            throw ValidationException::withMessages([
                'name' => 'A plan with this name already exists for this merchant.',
            ]);
        }

        if (
            $this->planRepository->existsByCode(
                $data['merchant_id'],
                $data['code']
            )
        ) {
            throw ValidationException::withMessages([
                'code' => 'A plan with this code already exists for this merchant.',
            ]);
        }

        return $this->planRepository->create($data);
    }

    /**
     * Update an existing plan.
     *
     * Cache is invalidated after the database update so that
     * the next lookup gets the latest pricing information.
     */
    public function update(int $id, array $data): Plan
    {
        $plan = $this->findById($id);

        if (
            isset($data['name']) &&
            $this->planRepository->existsByName(
                $plan->merchant_id,
                $data['name'],
                $plan->id
            )
        ) {
            throw ValidationException::withMessages([
                'name' => 'A plan with this name already exists for this merchant.',
            ]);
        }

        if (
            isset($data['code']) &&
            $this->planRepository->existsByCode(
                $plan->merchant_id,
                $data['code'],
                $plan->id
            )
        ) {
            throw ValidationException::withMessages([
                'code' => 'A plan with this code already exists for this merchant.',
            ]);
        }

        $updatedPlan = $this->planRepository->update(
            $plan,
            $data
        );

        /*
         * Invalidate the old cached plan.
         *
         * This is important when pricing fields such as base_price,
         * included_units, overage_rate or billing_cycle are changed.
         */
        Cache::forget(
            $this->getCacheKey($plan->id)
        );

        return $updatedPlan;
    }

    /**
     * Delete a plan.
     *
     * Cache is invalidated after successful deletion.
     */
    public function delete(int $id): bool
    {
        $plan = $this->findById($id);

        $deleted = $this->planRepository->delete($plan);

        if ($deleted) {
            Cache::forget(
                $this->getCacheKey($plan->id)
            );
        }

        return $deleted;
    }

    /**
     * Generate the cache key for a plan.
     */
    private function getCacheKey(int $id): string
    {
        return "plan:{$id}";
    }
}