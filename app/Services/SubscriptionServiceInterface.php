<?php

namespace App\Services;

use App\Models\Subscription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SubscriptionServiceInterface
{
    public function getAll(array $filters = []): LengthAwarePaginator;

    public function getById(int $id): Subscription;

    public function findById(int $id): ?Subscription;

    public function create(array $data): Subscription;

    public function update(int $id, array $data): Subscription;

    public function delete(int $id): bool;
}