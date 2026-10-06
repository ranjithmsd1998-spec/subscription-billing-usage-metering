<?php

namespace App\Services;

use App\Models\SubscriptionPeriod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SubscriptionPeriodServiceInterface
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function getById(int $id): SubscriptionPeriod;

    public function create(array $data): SubscriptionPeriod;

    public function update(int $id, array $data): SubscriptionPeriod;

    public function delete(int $id): void;
}