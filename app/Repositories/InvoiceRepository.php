<?php

namespace App\Repositories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function create(array $data): Invoice
    {
        return Invoice::create($data);
    }

    public function findById(int $id): ?Invoice
    {
        return Invoice::query()
            ->with([
                'merchant',
                'customer',
                'subscription',
                'subscriptionPeriod',
                'items',
            ])
            ->find($id);
    }

    public function findBySubscriptionPeriod(
        int $subscriptionPeriodId
    ): ?Invoice {
        return Invoice::query()
            ->with([
                'merchant',
                'customer',
                'subscription',
                'subscriptionPeriod',
                'items',
            ])
            ->where('subscription_period_id', $subscriptionPeriodId)
            ->first();
    }

    public function getByCustomer(int $customerId): Collection
    {
        return Invoice::query()
            ->with([
                'items',
                'subscription',
                'subscriptionPeriod',
            ])
            ->where('customer_id', $customerId)
            ->orderByDesc('id')
            ->get();
    }

    public function getByMerchant(int $merchantId): Collection
    {
        return Invoice::query()
            ->with([
                'customer',
                'subscription',
                'subscriptionPeriod',
                'items',
            ])
            ->where('merchant_id', $merchantId)
            ->orderByDesc('id')
            ->get();
    }

    public function update(
        Invoice $invoice,
        array $data
    ): Invoice {
        $invoice->update($data);

        return $invoice->fresh([
            'merchant',
            'customer',
            'subscription',
            'subscriptionPeriod',
            'items',
        ]);
    }

    public function createItem(
        Invoice $invoice,
        array $data
    ): InvoiceItem {
        return $invoice->items()->create($data);
    }
}