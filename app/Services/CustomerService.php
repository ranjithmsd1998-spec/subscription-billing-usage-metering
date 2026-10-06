<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerService implements CustomerServiceInterface
{
    public function __construct(
        protected CustomerRepositoryInterface $customerRepository
    ) {
    }

    /**
     * Get paginated customers.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->customerRepository->paginate($perPage);
    }

    /**
     * Get customer by ID.
     */
    public function findById(int $id): Customer
    {
        return $this->customerRepository->findById($id);
    }

    /**
     * Create a customer.
     */
    public function create(array $data): Customer
    {
        return $this->customerRepository->create($data);
    }

    /**
     * Update a customer.
     */
    public function update(int $id, array $data): Customer
    {
        return $this->customerRepository->update($id, $data);
    }

    /**
     * Delete a customer.
     */
    public function delete(int $id): bool
    {
        return $this->customerRepository->delete($id);
    }
}