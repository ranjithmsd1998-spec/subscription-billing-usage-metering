<?php

namespace App\Services;

use App\Models\Merchant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface MerchantServiceInterface
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
     * Get a merchant by ID.
     */
    public function findById(int $id): Merchant;

    /**
     * Create a new merchant.
     */
    public function create(array $data): Merchant;

    /**
     * Update an existing merchant.
     */
    public function update(int $id, array $data): Merchant;

    /**
     * Delete a merchant.
     */
    public function delete(int $id): bool;
}