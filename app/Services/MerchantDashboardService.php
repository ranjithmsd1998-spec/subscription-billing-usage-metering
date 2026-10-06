<?php

namespace App\Services;

use App\Models\Merchant;
use App\Repositories\MerchantDashboardRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class MerchantDashboardService implements MerchantDashboardServiceInterface
{
    public function __construct(
        protected MerchantDashboardRepositoryInterface $dashboardRepository
    ) {
    }

    /**
     * Get merchant dashboard data.
     */
    public function getDashboard(int $merchantId): array
    {
        $merchant = Merchant::query()->find($merchantId);

        if (!$merchant) {
            throw new ModelNotFoundException(
                'Merchant not found.'
            );
        }

        $now = Carbon::now();

        /*
         * ---------------------------------------------------------------
         * Current Month
         * ---------------------------------------------------------------
         */

        $currentMonthStart = $now->copy()
            ->startOfMonth();

        $currentMonthEnd = $now->copy()
            ->endOfMonth();

        /*
         * ---------------------------------------------------------------
         * Previous Month
         * ---------------------------------------------------------------
         */

        $previousMonthStart = $now->copy()
            ->subMonthNoOverflow()
            ->startOfMonth();

        $previousMonthEnd = $now->copy()
            ->subMonthNoOverflow()
            ->endOfMonth();

        /*
         * ---------------------------------------------------------------
         * 1. Top 5 Customers by Usage
         * ---------------------------------------------------------------
         */

        $topCustomers = $this->dashboardRepository
            ->getTopCustomersByUsage(
                $merchantId,
                $currentMonthStart->toDateTimeString(),
                $currentMonthEnd->toDateTimeString(),
                5
            )
            ->map(function ($item) {
                return [
                    'customer_id' => $item->customer_id,
                    'customer_name' => $item->customer?->name,
                    'customer_code' => $item->customer?->code,
                    'email' => $item->customer?->email,
                    'total_usage_units' => (int) $item->total_usage_units,
                    'event_count' => (int) $item->event_count,
                ];
            })
            ->values()
            ->toArray();

        /*
         * ---------------------------------------------------------------
         * 2. Projected Overage Revenue
         * ---------------------------------------------------------------
         *
         * We calculate the projected usage for the full current cycle:
         *
         * projected usage =
         * current usage / elapsed days * total cycle days
         *
         * projected overage units =
         * projected usage - included units
         *
         * projected revenue =
         * projected overage units * overage rate
         *
         */

        $projectedOverageRevenue = 0.0;

        $activeSubscriptions = $this->dashboardRepository
            ->getCurrentCycleSubscriptions(
                $merchantId,
                $currentMonthStart->toDateTimeString(),
                $currentMonthEnd->toDateTimeString()
            );

        foreach ($activeSubscriptions as $subscription) {

            $cycleStart = $subscription->current_period_start
                ? Carbon::parse($subscription->current_period_start)
                : null;

            $cycleEnd = $subscription->current_period_end
                ? Carbon::parse($subscription->current_period_end)
                : null;

            if (!$cycleStart || !$cycleEnd) {
                continue;
            }

            /*
             * Usage up to now for this subscription.
             */
            $usageUntilNow = $subscription
                ->usageEvents()
                ->whereBetween('occurred_at', [
                    $cycleStart->toDateTimeString(),
                    min(
                        $now->timestamp,
                        $cycleEnd->timestamp
                    ) === $cycleEnd->timestamp
                        ? $cycleEnd->toDateTimeString()
                        : $now->toDateTimeString(),
                ])
                ->sum('usage_units');

            $totalCycleDays = max(
                1,
                $cycleStart->diffInDays($cycleEnd)
            );

            $elapsedCycleDays = max(
                1,
                min(
                    $totalCycleDays,
                    $cycleStart->diffInDays(
                        $now->greaterThan($cycleEnd)
                            ? $cycleEnd
                            : $now
                    )
                )
            );

            /*
             * If the cycle has not started yet, do not project usage.
             */
            if ($now->lessThan($cycleStart)) {
                continue;
            }

            $projectedUsage = (
                $usageUntilNow / $elapsedCycleDays
            ) * $totalCycleDays;

            $includedUnits = (float) (
                $subscription->plan?->included_units ?? 0
            );

            $overageRate = (float) (
                $subscription->plan?->overage_rate ?? 0
            );

            $projectedOverageUnits = max(
                0,
                $projectedUsage - $includedUnits
            );

            $projectedOverageRevenue += (
                $projectedOverageUnits * $overageRate
            );
        }

        /*
         * ---------------------------------------------------------------
         * 3. Customers with >50% MoM Usage Drop
         * ---------------------------------------------------------------
         */

        $currentUsage = $this->dashboardRepository
            ->getCustomerUsageByPeriod(
                $merchantId,
                $currentMonthStart->toDateTimeString(),
                $currentMonthEnd->toDateTimeString()
            )
            ->keyBy('customer_id');

        $previousUsage = $this->dashboardRepository
            ->getCustomerUsageByPeriod(
                $merchantId,
                $previousMonthStart->toDateTimeString(),
                $previousMonthEnd->toDateTimeString()
            )
            ->keyBy('customer_id');

        $customerIds = $previousUsage
            ->keys()
            ->merge($currentUsage->keys())
            ->unique();

        $usageDroppedCustomers = [];

        foreach ($customerIds as $customerId) {

            $previousUnits = (float) (
                $previousUsage->get($customerId)?->total_usage_units ?? 0
            );

            $currentUnits = (float) (
                $currentUsage->get($customerId)?->total_usage_units ?? 0
            );

            /*
             * A customer needs previous-month usage greater than zero
             * for a percentage drop to be meaningful.
             */
            if ($previousUnits <= 0) {
                continue;
            }

            $dropPercentage = (
                ($previousUnits - $currentUnits)
                / $previousUnits
            ) * 100;

            if ($dropPercentage > 50) {

                $customer = $this->getCustomerFromUsageData(
                    $customerId,
                    $merchantId
                );

                $usageDroppedCustomers[] = [
                    'customer_id' => $customerId,
                    'customer_name' => $customer?->name,
                    'customer_code' => $customer?->code,
                    'previous_month_usage' => $previousUnits,
                    'current_month_usage' => $currentUnits,
                    'drop_percentage' => round(
                        $dropPercentage,
                        2
                    ),
                ];
            }
        }

        /*
         * Sort customers by highest percentage drop first.
         */
        usort(
            $usageDroppedCustomers,
            function (array $a, array $b) {
                return $b['drop_percentage']
                    <=> $a['drop_percentage'];
            }
        );

        return [
            'merchant' => [
                'id' => $merchant->id,
                'name' => $merchant->name,
                'code' => $merchant->code,
                'currency' => $merchant->currency,
            ],

            'period' => [
                'current_month_start' =>
                    $currentMonthStart->toDateString(),

                'current_month_end' =>
                    $currentMonthEnd->toDateString(),

                'previous_month_start' =>
                    $previousMonthStart->toDateString(),

                'previous_month_end' =>
                    $previousMonthEnd->toDateString(),
            ],

            'top_5_customers_by_usage' => $topCustomers,

            'projected_overage_revenue' => [
                'amount' => round(
                    $projectedOverageRevenue,
                    2
                ),
                'currency' => $merchant->currency,
            ],

            'customers_usage_dropped_over_50_percent' =>
                $usageDroppedCustomers,
        ];
    }

    /**
     * Get customer information for a usage record.
     */
    private function getCustomerFromUsageData(
        int $customerId,
        int $merchantId
    ): ?object {
        return \App\Models\Customer::query()
            ->where('id', $customerId)
            ->where('merchant_id', $merchantId)
            ->first([
                'id',
                'name',
                'code',
                'email',
            ]);
    }
}