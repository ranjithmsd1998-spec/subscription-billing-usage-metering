<?php

namespace App\Jobs;

use App\Models\DailyUsageAggregate;
use App\Models\SubscriptionPeriod;
use App\Models\UsageEvent;
use App\Repositories\DailyUsageAggregateRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AggregateDailyUsageJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public array $backoff = [30, 60, 120];

    public int $timeout = 120;

    public function __construct(
        public int $subscriptionPeriodId,
        public string $usageDate
    ) {
    }

    public function handle(
        DailyUsageAggregateRepositoryInterface $repository
    ): void {
        $period = SubscriptionPeriod::query()
            ->with('subscription')
            ->find($this->subscriptionPeriodId);

        if (!$period) {
            return;
        }

        if (!$period->subscription) {
            return;
        }

        $date = Carbon::parse($this->usageDate)->startOfDay();

        /*
         * The requested usage date must belong to this
         * subscription period.
         */
        if (!$period->containsDate($date)) {
            return;
        }

        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        $totalUsageUnits = 0;
        $eventCount = 0;

        /*
         * Process usage events in chunks so a large number
         * of usage-event rows does not get loaded into memory.
         */
        UsageEvent::query()
            ->select([
                'id',
                'usage_units',
                'occurred_at',
            ])
            ->where(
                'subscription_period_id',
                $period->id
            )
            ->whereBetween(
                'occurred_at',
                [$startOfDay, $endOfDay]
            )
            ->orderBy('id')
            ->chunkById(
                1000,
                function ($events) use (
                    &$totalUsageUnits,
                    &$eventCount
                ): void {
                    foreach ($events as $event) {
                        $totalUsageUnits += (int) $event->usage_units;
                        $eventCount++;
                    }
                },
                'id'
            );

        DB::transaction(function () use (
            $repository,
            $period,
            $date,
            $totalUsageUnits,
            $eventCount
        ): void {
            $data = [
                'merchant_id' => $period->subscription->merchant_id,
                'customer_id' => $period->subscription->customer_id,
                'subscription_id' => $period->subscription_id,
                'subscription_period_id' => $period->id,
                'usage_date' => $date->toDateString(),
                'total_usage_units' => $totalUsageUnits,
                'event_count' => $eventCount,
            ];

            $existingAggregate = $repository
                ->findBySubscriptionPeriodAndDate(
                    $period->id,
                    $date
                );

            if ($existingAggregate) {
                $repository->update(
                    $existingAggregate,
                    [
                        'total_usage_units' => $totalUsageUnits,
                        'event_count' => $eventCount,
                    ]
                );

                return;
            }

            $repository->create($data);
        });

        /*
         * Once the billing period has ended, dispatch the
         * cycle invoice generation job.
         */
        if (
            $period->ends_at !== null
            && $period->ends_at->lte(now())
        ) {
            GenerateCycleInvoiceJob::dispatch(
                $period->id
            );
        }
    }
}