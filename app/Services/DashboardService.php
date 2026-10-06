<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Merchant;
use App\Models\Subscription;
use App\Models\UsageEvent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService implements DashboardServiceInterface
{
    /**
     * Get all data required for the dashboard.
     */
    public function getDashboardData(): array
    {
        $now = now();

        $currentMonthStart = $now->copy()->startOfMonth();
        $currentMonthEnd = $now->copy()->endOfMonth();

        $previousMonthStart = $now
            ->copy()
            ->subMonthNoOverflow()
            ->startOfMonth();

        $previousMonthEnd = $now
            ->copy()
            ->subMonthNoOverflow()
            ->endOfMonth();

        $currentMonthUsage = $this->getUsageBetween(
            $currentMonthStart,
            $currentMonthEnd
        );

        $previousMonthUsage = $this->getUsageBetween(
            $previousMonthStart,
            $previousMonthEnd
        );

        return [
            'stats' => [
                'total_merchants' => Merchant::query()->count(),

                'total_customers' => Customer::query()->count(),

                'active_subscriptions' => Subscription::query()
                    ->where('status', 'active')
                    ->count(),

                'current_month_usage' => $currentMonthUsage,

                'previous_month_usage' => $previousMonthUsage,

                'usage_growth_percentage' => $this->calculateGrowthPercentage(
                    $currentMonthUsage,
                    $previousMonthUsage
                ),

                'projected_overage_revenue' => $this->getProjectedOverageRevenue(),
            ],

            'top_customers' => $this->getTopCustomers(
                $currentMonthStart,
                $currentMonthEnd
            ),

            'usage_drop_customers' => $this->getUsageDropCustomers(
                $currentMonthStart,
                $currentMonthEnd,
                $previousMonthStart,
                $previousMonthEnd
            ),

            'recent_invoices' => $this->getRecentInvoices(),
        ];
    }

    /**
     * Get total usage between two dates.
     */
    private function getUsageBetween(
        $start,
        $end
    ): int {
        return (int) UsageEvent::query()
            ->whereBetween('occurred_at', [$start, $end])
            ->sum('usage_units');
    }

    /**
     * Calculate percentage growth.
     */
    private function calculateGrowthPercentage(
        int $currentUsage,
        int $previousUsage
    ): ?float {
        if ($previousUsage === 0) {
            return null;
        }

        return round(
            (($currentUsage - $previousUsage) / $previousUsage) * 100,
            1
        );
    }

    /**
     * Get top 5 customers by current month usage.
     */
    private function getTopCustomers(
        $start,
        $end
    ): Collection {
        return UsageEvent::query()
            ->select(
                'customer_id',
                DB::raw('SUM(usage_units) AS total_usage_units')
            )
            ->with([
                'customer:id,name,code',
            ])
            ->whereBetween('occurred_at', [$start, $end])
            ->groupBy('customer_id')
            ->orderByDesc('total_usage_units')
            ->limit(5)
            ->get();
    }

    /**
     * Get customers whose usage dropped by more than 50% month-over-month.
     */
    private function getUsageDropCustomers(
        $currentMonthStart,
        $currentMonthEnd,
        $previousMonthStart,
        $previousMonthEnd
    ): Collection {
        $currentUsage = UsageEvent::query()
            ->select(
                'customer_id',
                DB::raw('SUM(usage_units) AS total_usage_units')
            )
            ->whereBetween(
                'occurred_at',
                [
                    $currentMonthStart,
                    $currentMonthEnd,
                ]
            )
            ->groupBy('customer_id')
            ->pluck(
                'total_usage_units',
                'customer_id'
            );

        $previousUsage = UsageEvent::query()
            ->select(
                'customer_id',
                DB::raw('SUM(usage_units) AS total_usage_units')
            )
            ->whereBetween(
                'occurred_at',
                [
                    $previousMonthStart,
                    $previousMonthEnd,
                ]
            )
            ->groupBy('customer_id')
            ->pluck(
                'total_usage_units',
                'customer_id'
            );

        $customerIds = $previousUsage
            ->filter(
                fn ($usage) => (int) $usage > 0
            )
            ->keys();

        if ($customerIds->isEmpty()) {
            return collect();
        }

        $customers = Customer::query()
            ->whereIn('id', $customerIds)
            ->get([
                'id',
                'name',
                'code',
            ]);

        return $customers
            ->map(function ($customer) use (
                $currentUsage,
                $previousUsage
            ) {
                $current = (int) (
                    $currentUsage[$customer->id] ?? 0
                );

                $previous = (int) (
                    $previousUsage[$customer->id] ?? 0
                );

                if ($previous <= 0) {
                    return null;
                }

                $dropPercentage = (
                    ($previous - $current) / $previous
                ) * 100;

                return [
                    'customer' => $customer,

                    'current_usage' => $current,

                    'previous_usage' => $previous,

                    'drop_percentage' => round(
                        $dropPercentage,
                        1
                    ),
                ];
            })
            ->filter()
            ->filter(
                fn ($item) =>
                    $item['drop_percentage'] > 50
            )
            ->sortByDesc('drop_percentage')
            ->take(5)
            ->values();
    }

    /**
     * Get projected overage revenue for active subscriptions.
     *
     * Projection:
     *
     * projected usage
     * = usage consumed so far
     *   / elapsed cycle duration
     *   * total cycle duration
     *
     * projected overage units
     * = projected usage - included units
     *
     * projected revenue
     * = projected overage units * overage rate
     */
    private function getProjectedOverageRevenue(): float
    {
        $now = now();

        $subscriptions = Subscription::query()
            ->with([
                'plan:id,name,included_units,overage_rate',
            ])
            ->where('status', 'active')
            ->whereNotNull('current_period_start')
            ->whereNotNull('current_period_end')
            ->get();

        $projectedRevenue = 0.0;

        foreach ($subscriptions as $subscription) {
            $plan = $subscription->plan;

            if (!$plan) {
                continue;
            }

            $includedUnits = (int) $plan->included_units;

            $overageRate = (float) $plan->overage_rate;

            if ($overageRate <= 0) {
                continue;
            }

            $periodStart = $subscription->current_period_start;
            $periodEnd = $subscription->current_period_end;

            if (!$periodStart || !$periodEnd) {
                continue;
            }

            if ($periodEnd->lessThanOrEqualTo($periodStart)) {
                continue;
            }

            /*
             * If the cycle has not started yet,
             * there is no usage projection.
             */
            if ($now->lessThan($periodStart)) {
                continue;
            }

            /*
             * Do not project beyond the current cycle end.
             */
            $effectiveNow = $now->greaterThan($periodEnd)
                ? $periodEnd
                : $now;

            $usageUntilNow = (int) UsageEvent::query()
                ->where('subscription_id', $subscription->id)
                ->whereBetween(
                    'occurred_at',
                    [
                        $periodStart,
                        $effectiveNow,
                    ]
                )
                ->sum('usage_units');

            $totalCycleSeconds = max(
                1,
                $periodStart->diffInSeconds($periodEnd)
            );

            $elapsedCycleSeconds = max(
                1,
                $periodStart->diffInSeconds($effectiveNow)
            );

            /*
             * If the cycle has just started,
             * avoid an unrealistic division.
             */
            $elapsedCycleSeconds = min(
                $elapsedCycleSeconds,
                $totalCycleSeconds
            );

            $projectedUsage = (
                $usageUntilNow
                / $elapsedCycleSeconds
            ) * $totalCycleSeconds;

            $projectedOverageUnits = max(
                0,
                $projectedUsage - $includedUnits
            );

            $projectedRevenue +=
                $projectedOverageUnits * $overageRate;
        }

        return round($projectedRevenue, 2);
    }

    /**
     * Get latest invoices.
     */
    private function getRecentInvoices(): Collection
    {
        return Invoice::query()
            ->with([
                'customer:id,name',
            ])
            ->latest('id')
            ->limit(8)
            ->get();
    }
}