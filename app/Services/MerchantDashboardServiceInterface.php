<?php

namespace App\Services;

interface MerchantDashboardServiceInterface
{
    /**
     * Get merchant dashboard data.
     */
    public function getDashboard(int $merchantId): array;
}