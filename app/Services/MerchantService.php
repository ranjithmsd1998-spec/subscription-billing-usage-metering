<?php

namespace App\Services;

use App\Models\Merchant;
use App\Repositories\MerchantRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class MerchantService implements MerchantServiceInterface
{
    public function __construct(
        protected MerchantRepositoryInterface $merchantRepository
    ) {
    }

    /**
     * Get all merchants.
     */
    public function getAll(): Collection
    {
        return $this->merchantRepository->getAll();
    }

    /**
     * Get paginated merchants.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->merchantRepository->paginate($perPage);
    }

    /**
     * Get a merchant by ID.
     */
    public function findById(int $id): Merchant
    {
        $merchant = $this->merchantRepository->findById($id);

        if (!$merchant) {
            throw new ModelNotFoundException(
                'Merchant not found.'
            );
        }

        return $merchant;
    }

    /**
     * Create a new merchant.
     */
    public function create(array $data): Merchant
    {
        if ($this->merchantRepository->existsByEmail($data['email'])) {
            throw ValidationException::withMessages([
                'email' => 'A merchant with this email already exists.',
            ]);
        }

        return $this->merchantRepository->create($data);
    }

    /**
     * Update an existing merchant.
     */
    public function update(int $id, array $data): Merchant
    {
        $merchant = $this->findById($id);

        if (
            isset($data['email']) &&
            $this->merchantRepository->existsByEmail(
                $data['email'],
                $merchant->id
            )
        ) {
            throw ValidationException::withMessages([
                'email' => 'A merchant with this email already exists.',
            ]);
        }

        return $this->merchantRepository->update(
            $merchant,
            $data
        );
    }

    /**
     * Delete an existing merchant.
     */
    public function delete(int $id): bool
    {
        $merchant = $this->findById($id);

        return $this->merchantRepository->delete($merchant);
    }
}