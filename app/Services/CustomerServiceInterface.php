<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CustomerServiceInterface
{
    /**
     * Get paginated customers.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get customer by ID.
     */
    public function findById(int $id): Customer;

    /**
     * Create a customer.
     */
    public function create(array $data): Customer;

    /**
     * Update a customer.
     */
    public function update(int $id, array $data): Customer;

    /**
     * Delete a customer.
     */
    public function delete(int $id): bool;
}