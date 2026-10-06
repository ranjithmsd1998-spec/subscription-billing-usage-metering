<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlanChange\StorePlanChangeRequest;
use App\Services\PlanChangeServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanChangeWebController extends Controller
{
    public function __construct(
        private readonly PlanChangeServiceInterface $planChangeService
    ) {
    }

    /**
     * Display the Plan Change page.
     */
    public function index()
    {
        return view('plan-changes.index');
    }

    /**
     * Get plan change history for a subscription.
     */
    public function subscriptionHistory(
        int $subscription
    ): JsonResponse {
        $planChanges = $this->planChangeService
            ->getBySubscriptionId($subscription);

        return response()->json([
            'success' => true,
            'message' => 'Subscription plan change history retrieved successfully.',
            'data' => $planChanges,
        ]);
    }

    /**
     * Store a new plan change.
     */
    public function store(
        StorePlanChangeRequest $request
    ): JsonResponse {
        $planChange = $this->planChangeService->create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Plan change created successfully.',
            'data' => $planChange,
        ], 201);
    }

    /**
     * Display a single plan change.
     */
    public function show(int $id): JsonResponse
    {
        $planChange = $this->planChangeService->getById($id);

        return response()->json([
            'success' => true,
            'message' => 'Plan change retrieved successfully.',
            'data' => $planChange,
        ]);
    }
}