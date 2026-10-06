<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceServiceInterface
{
    public function generate(
        int $subscriptionPeriodId,
        float $taxRate = 0,
        ?string $dueAt = null
    ): Invoice;

    public function findById(int $id): Invoice;

    public function getByCustomer(int $customerId): Collection;

    public function getByMerchant(int $merchantId): Collection;

    public function issue(int $id): Invoice;

    public function markAsPaid(int $id): Invoice;

    public function void(int $id): Invoice;
}