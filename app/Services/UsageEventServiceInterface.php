<?php

namespace App\Services;

use App\Models\UsageEvent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UsageEventServiceInterface
{
    public function getAll(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator;

    public function getById(int $id): UsageEvent;

    public function create(array $data): UsageEvent;

    public function update(int $id, array $data): UsageEvent;

    public function delete(int $id): void;
}