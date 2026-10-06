<?php

namespace App\Repositories;

use App\Models\Merchant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface MerchantRepositoryInterface
{
    /**
     * Get all merchants.
     */
    public function getAll(): Collection;

    /**
     * Get paginated merchants.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a merchant by ID.
     */
    public function findById(int $id): ?Merchant;

    /**
     * Create a new merchant.
     */
    public function create(array $data): Merchant;

    /**
     * Update an existing merchant.
     */
    public function update(Merchant $merchant, array $data): Merchant;

    /**
     * Delete a merchant.
     */
    public function delete(Merchant $merchant): bool;

    /**
     * Check whether a merchant exists by email.
     */
    public function existsByEmail(
        string $email,
        ?int $ignoreId = null
    ): bool;
}