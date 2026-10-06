<?php

namespace App\Repositories;

use App\Models\UsageEvent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UsageEventRepositoryInterface
{
    public function getAll(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator;

    public function findById(int $id): UsageEvent;

    public function create(array $data): UsageEvent;

    public function update(
        UsageEvent $usageEvent,
        array $data
    ): UsageEvent;

    public function delete(UsageEvent $usageEvent): void;

    public function findByIdempotencyKey(
        int $merchantId,
        string $idempotencyKey
    ): ?UsageEvent;
}