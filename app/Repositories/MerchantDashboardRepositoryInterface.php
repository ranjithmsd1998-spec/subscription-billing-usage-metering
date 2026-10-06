<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface MerchantDashboardRepositoryInterface
{
    /**
     * Get top customers by usage for the current month.
     */
    public function getTopCustomersByUsage(
        int $merchantId,
        string $monthStart,
        string $monthEnd,
        int $limit = 5
    ): Collection;

    /**
     * Get active subscriptions for the merchant's current cycle.
     */
    public function getCurrentCycleSubscriptions(
        int $merchantId,
        string $cycleStart,
        string $cycleEnd
    ): Collection;

    /**
     * Get customer usage for a specific date range.
     */
    public function getCustomerUsageByPeriod(
        int $merchantId,
        string $periodStart,
        string $periodEnd
    ): Collection;
}