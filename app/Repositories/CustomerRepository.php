<?php

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerRepository implements CustomerRepositoryInterface
{
    /**
     * Get paginated customers.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Customer::query()
            ->with('merchant')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find customer by ID.
     */
    public function findById(int $id): Customer
    {
        return Customer::query()
            ->with('merchant')
            ->findOrFail($id);
    }

    /**
     * Create a customer.
     */
    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    /**
     * Update a customer.
     */
    public function update(int $id, array $data): Customer
    {
        $customer = Customer::findOrFail($id);

        $customer->update($data);

        return $customer->fresh();
    }

    /**
     * Delete a customer.
     */
    public function delete(int $id): bool
    {
        $customer = Customer::findOrFail($id);

        return (bool) $customer->delete();
    }
}