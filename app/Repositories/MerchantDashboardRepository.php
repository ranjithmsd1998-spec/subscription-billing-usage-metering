<?php

namespace App\Repositories;

use App\Models\Subscription;
use App\Models\UsageEvent;
use Illuminate\Support\Collection;

class MerchantDashboardRepository implements MerchantDashboardRepositoryInterface
{
    /**
     * Get top customers by usage for the current month.
     */
    public function getTopCustomersByUsage(
        int $merchantId,
        string $monthStart,
        string $monthEnd,
        int $limit = 5
    ): Collection {
        return UsageEvent::query()
            ->selectRaw('
                customer_id,
                SUM(usage_units) as total_usage_units,
                COUNT(*) as event_count
            ')
            ->with([
                'customer:id,name,code,email',
            ])
            ->where('merchant_id', $merchantId)
            ->whereBetween('occurred_at', [
                $monthStart,
                $monthEnd,
            ])
            ->groupBy('customer_id')
            ->orderByDesc('total_usage_units')
            ->limit($limit)
            ->get();
    }

    /**
     * Get active subscriptions for the merchant's current cycle.
     */
    public function getCurrentCycleSubscriptions(
        int $merchantId,
        string $cycleStart,
        string $cycleEnd
    ): Collection {
        return Subscription::query()
            ->with([
                'customer:id,name,code,email',
                'plan:id,name,code,included_units,overage_rate,unit_name,currency',
            ])
            ->where('merchant_id', $merchantId)
            ->where('status', 'active')
            ->where(function ($query) use ($cycleStart, $cycleEnd) {
                $query
                    ->whereBetween('current_period_start', [
                        $cycleStart,
                        $cycleEnd,
                    ])
                    ->orWhereBetween('current_period_end', [
                        $cycleStart,
                        $cycleEnd,
                    ])
                    ->orWhere(function ($query) use ($cycleStart, $cycleEnd) {
                        $query
                            ->where('current_period_start', '<=', $cycleStart)
                            ->where('current_period_end', '>=', $cycleEnd);
                    });
            })
            ->get();
    }

    /**
     * Get customer usage for a date range.
     */
    public function getCustomerUsageByPeriod(
        int $merchantId,
        string $periodStart,
        string $periodEnd
    ): Collection {
        return UsageEvent::query()
            ->selectRaw('
                customer_id,
                SUM(usage_units) as total_usage_units
            ')
            ->where('merchant_id', $merchantId)
            ->whereBetween('occurred_at', [
                $periodStart,
                $periodEnd,
            ])
            ->groupBy('customer_id')
            ->get();
    }
}