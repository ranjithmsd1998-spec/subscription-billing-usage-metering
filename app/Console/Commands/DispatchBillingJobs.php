<?php

namespace App\Console\Commands;

use App\Jobs\AggregateDailyUsageJob;
use App\Jobs\GenerateCycleInvoiceJob;
use App\Models\SubscriptionPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DispatchBillingJobs extends Command
{
    protected $signature = 'billing:dispatch
                            {--date= : Usage date in Y-m-d format. Defaults to today.}';

    protected $description = 'Dispatch daily usage aggregation and cycle invoice jobs.';

    public function handle(): int
    {
        $dateOption = $this->option('date');

        try {
            $runDate = $dateOption
                ? Carbon::createFromFormat('Y-m-d', $dateOption)->startOfDay()
                : now()->startOfDay();
        } catch (\Throwable) {
            $this->error('Invalid date. Please use Y-m-d format, for example 2026-10-05.');

            return self::FAILURE;
        }

        $this->info(
            'Processing billing jobs for: ' . $runDate->toDateString()
        );

        $dailyJobs = 0;
        $invoiceJobs = 0;

        /*
         * ---------------------------------------------------------
         * 1. Current billing periods
         * ---------------------------------------------------------
         *
         * Dispatch daily usage aggregation for periods which
         * contain the requested date.
         */
        SubscriptionPeriod::query()
            ->where('starts_at', '<=', $runDate->copy()->endOfDay())
            ->where(function ($query) use ($runDate) {
                $query
                    ->whereNull('ends_at')
                    ->orWhere(
                        'ends_at',
                        '>=',
                        $runDate->copy()->startOfDay()
                    );
            })
            ->orderBy('id')
            ->chunkById(100, function ($periods) use (
                $runDate,
                &$dailyJobs
            ): void {
                foreach ($periods as $period) {
                    AggregateDailyUsageJob::dispatch(
                        (int) $period->id,
                        $runDate->toDateString()
                    );

                    $dailyJobs++;
                }
            });

        /*
         * ---------------------------------------------------------
         * 2. Completed billing periods
         * ---------------------------------------------------------
         *
         * Find periods which have ended and do not already have
         * an invoice.
         *
         * The NOT EXISTS query prevents dispatching unnecessary
         * invoice jobs for already-invoiced periods.
         */
        SubscriptionPeriod::query()
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->whereNotExists(function ($query) {
                $query
                    ->select(DB::raw(1))
                    ->from('invoices')
                    ->whereColumn(
                        'invoices.subscription_period_id',
                        'subscription_periods.id'
                    );
            })
            ->orderBy('id')
            ->chunkById(100, function ($periods) use (
                &$invoiceJobs
            ): void {
                foreach ($periods as $period) {
                    /*
                     * Aggregate the final day as well.
                     */
                    AggregateDailyUsageJob::dispatch(
                        (int) $period->id,
                        $period->ends_at->toDateString()
                    );

                    /*
                     * Generate the cycle-end invoice.
                     */
                    GenerateCycleInvoiceJob::dispatch(
                        (int) $period->id
                    );

                    $invoiceJobs++;
                }
            });

        $this->newLine();

        $this->info(
            "Daily aggregation jobs dispatched: {$dailyJobs}"
        );

        $this->info(
            "Invoice generation jobs dispatched: {$invoiceJobs}"
        );

        $this->info('Billing job dispatch completed.');

        return self::SUCCESS;
    }
}