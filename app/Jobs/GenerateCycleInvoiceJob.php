<?php

namespace App\Jobs;

use App\Models\SubscriptionPeriod;
use App\Repositories\InvoiceRepositoryInterface;
use App\Services\InvoiceServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateCycleInvoiceJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 120, 300];

    public int $timeout = 120;

    public function __construct(
        public int $subscriptionPeriodId,
        public float $taxRate = 0,
        public ?string $dueAt = null
    ) {
    }

    public function handle(
        InvoiceServiceInterface $invoiceService,
        InvoiceRepositoryInterface $invoiceRepository
    ): void {
        $period = SubscriptionPeriod::query()
            ->find($this->subscriptionPeriodId);

        if (!$period) {
            return;
        }

        /*
         * An invoice can only be generated after the
         * subscription billing period has ended.
         */
        if ($period->ends_at === null) {
            return;
        }

        if ($period->ends_at->isFuture()) {
            return;
        }

        /*
         * Prevent duplicate invoice generation if the job
         * is retried or dispatched more than once.
         */
        $existingInvoice = $invoiceRepository
            ->findBySubscriptionPeriod(
                $period->id
            );

        if ($existingInvoice) {
            return;
        }

        /*
         * InvoiceService is responsible for:
         *
         * - usage calculation
         * - included units
         * - overage calculation
         * - proration
         * - tax
         * - invoice items
         * - invoice number
         * - duplicate validation
         */
        $invoiceService->generate(
            $period->id,
            $this->taxRate,
            $this->dueAt
        );
    }
}