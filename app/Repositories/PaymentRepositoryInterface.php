<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

interface PaymentRepositoryInterface
{
    public function create(array $data): Payment;

    public function findById(int $id): ?Payment;

    public function findByReference(
        int $merchantId,
        string $paymentReference
    ): ?Payment;

    public function getByInvoice(int $invoiceId): Collection;

    public function getByCustomer(int $customerId): Collection;

    public function getByMerchant(int $merchantId): Collection;

    public function update(Payment $payment, array $data): Payment;
}