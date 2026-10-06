<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanChange\StorePlanChangeRequest;
use App\Http\Requests\PlanChange\UpdatePlanChangeRequest;
use App\Http\Resources\PlanChangeResource;
use App\Services\PlanChangeServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanChangeController extends Controller
{
    public function __construct(
        protected PlanChangeServiceInterface $planChangeService
    ) {
    }

    /**
     * Display a paginated list of plan changes.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'subscription_id',
            'from_plan_id',
            'to_plan_id',
            'change_type',
            'per_page',
        ]);

        $planChanges = $this->planChangeService->getAll(
            $filters
        );

        return response()->json([
            'success' => true,
            'message' => 'Plan changes retrieved successfully.',
            'data' => PlanChangeResource::collection(
                $planChanges
            ),
        ], 200);
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
            'data' => new PlanChangeResource(
                $planChange
            ),
        ], 201);
    }

    /**
     * Display a single plan change.
     */
    public function show(
        int $planChange
    ): JsonResponse {
        $planChangeModel = $this->planChangeService->getById(
            $planChange
        );

        return response()->json([
            'success' => true,
            'message' => 'Plan change retrieved successfully.',
            'data' => new PlanChangeResource(
                $planChangeModel
            ),
        ], 200);
    }

    /**
     * Update an existing plan change.
     */
    public function update(
        UpdatePlanChangeRequest $request,
        int $planChange
    ): JsonResponse {
        $planChangeModel = $this->planChangeService->update(
            $planChange,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Plan change updated successfully.',
            'data' => new PlanChangeResource(
                $planChangeModel
            ),
        ], 200);
    }

    /**
     * Delete a plan change.
     */
    public function destroy(
        int $planChange
    ): JsonResponse {
        $this->planChangeService->delete(
            $planChange
        );

        return response()->json([
            'success' => true,
            'message' => 'Plan change deleted successfully.',
            'data' => null,
        ], 200);
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
            'data' => PlanChangeResource::collection(
                $planChanges
            ),
        ], 200);
    }
}