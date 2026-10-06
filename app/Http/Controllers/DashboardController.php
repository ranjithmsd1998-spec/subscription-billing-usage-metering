<?php

namespace App\Http\Controllers;

use App\Services\DashboardServiceInterface;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardServiceInterface $dashboardService
    ) {
    }

    /**
     * Display dashboard.
     */
    public function index(): View
    {
        $dashboardData = $this->dashboardService
            ->getDashboardData();

        return view(
            'dashboard.index',
            $dashboardData
        );
    }
}