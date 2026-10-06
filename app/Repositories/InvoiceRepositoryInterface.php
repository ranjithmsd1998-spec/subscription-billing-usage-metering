<?php

namespace App\Repositories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceRepositoryInterface
{
    public function create(array $data): Invoice;

    public function findById(int $id): ?Invoice;

    public function findBySubscriptionPeriod(
        int $subscriptionPeriodId
    ): ?Invoice;

    public function getByCustomer(int $customerId): Collection;

    public function getByMerchant(int $merchantId): Collection;

    public function update(
        Invoice $invoice,
        array $data
    ): Invoice;

    public function createItem(
        Invoice $invoice,
        array $data
    ): \App\Models\InvoiceItem;
}