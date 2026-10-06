<?php

namespace App\Repositories;

use App\Models\Merchant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MerchantRepository implements MerchantRepositoryInterface
{
    /**
     * Get all merchants.
     */
    public function getAll(): Collection
    {
        return Merchant::query()
            ->latest()
            ->get();
    }

    /**
     * Get paginated merchants.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Merchant::query()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find a merchant by ID.
     */
    public function findById(int $id): ?Merchant
    {
        return Merchant::find($id);
    }

    /**
     * Create a new merchant.
     */
    public function create(array $data): Merchant
    {
        return Merchant::create($data);
    }

    /**
     * Update an existing merchant.
     */
    public function update(Merchant $merchant, array $data): Merchant
    {
        $merchant->update($data);

        return $merchant->refresh();
    }

    /**
     * Delete a merchant.
     */
    public function delete(Merchant $merchant): bool
    {
        return (bool) $merchant->delete();
    }

    /**
     * Check whether a merchant exists by email.
     */
    public function existsByEmail(
        string $email,
        ?int $ignoreId = null
    ): bool {
        return Merchant::query()
            ->where('email', $email)
            ->when(
                $ignoreId !== null,
                fn ($query) => $query->whereKeyNot($ignoreId)
            )
            ->exists();
    }
}