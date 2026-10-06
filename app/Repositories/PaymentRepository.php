<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function findById(int $id): ?Payment
    {
        return Payment::with([
            'merchant',
            'customer',
            'invoice',
        ])->find($id);
    }

    public function findByReference(
        int $merchantId,
        string $paymentReference
    ): ?Payment {
        return Payment::where('merchant_id', $merchantId)
            ->where('payment_reference', $paymentReference)
            ->first();
    }

    public function getByInvoice(int $invoiceId): Collection
    {
        return Payment::with([
            'merchant',
            'customer',
            'invoice',
        ])
            ->where('invoice_id', $invoiceId)
            ->latest()
            ->get();
    }

    public function getByCustomer(int $customerId): Collection
    {
        return Payment::with([
            'merchant',
            'customer',
            'invoice',
        ])
            ->where('customer_id', $customerId)
            ->latest()
            ->get();
    }

    public function getByMerchant(int $merchantId): Collection
    {
        return Payment::with([
            'merchant',
            'customer',
            'invoice',
        ])
            ->where('merchant_id', $merchantId)
            ->latest()
            ->get();
    }

    public function update(
        Payment $payment,
        array $data
    ): Payment {
        $payment->update($data);

        return $payment->fresh([
            'merchant',
            'customer',
            'invoice',
        ]);
    }
}