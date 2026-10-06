<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MerchantDashboardServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class MerchantDashboardController extends Controller
{
    public function __construct(
        protected MerchantDashboardServiceInterface $dashboardService
    ) {
    }

    /**
     * Get merchant dashboard.
     */
    public function show(int $merchant): JsonResponse
    {
        try {

            $data = $this->dashboardService
                ->getDashboard($merchant);

            return response()->json([
                'success' => true,
                'message' => 'Merchant dashboard retrieved successfully.',
                'data' => $data,
            ]);

        } catch (ModelNotFoundException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Merchant not found.',
                'data' => null,
            ], 404);

        } catch (Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve merchant dashboard.',
                'data' => null,
            ], 500);
        }
    }
}