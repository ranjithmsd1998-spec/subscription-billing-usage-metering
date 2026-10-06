<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\StorePlanRequest;
use App\Http\Requests\Plan\UpdatePlanRequest;
use App\Http\Resources\PlanResource;
use App\Services\PlanServiceInterface;
use Illuminate\Http\JsonResponse;

class PlanController extends Controller
{
    public function __construct(
        protected PlanServiceInterface $planService
    ) {
    }

    /**
     * Display a paginated list of plans.
     */
    public function index(): JsonResponse
    {
        $plans = $this->planService->paginate();

        return response()->json([
            'success' => true,
            'message' => 'Plans retrieved successfully.',
            'data' => PlanResource::collection($plans),
        ], 200);
    }

    /**
     * Store a newly created plan.
     */
    public function store(
        StorePlanRequest $request
    ): JsonResponse {
        $plan = $this->planService->create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Plan created successfully.',
            'data' => new PlanResource($plan),
        ], 201);
    }

    /**
     * Display the specified plan.
     */
    public function show(int $plan): JsonResponse
    {
        $planModel = $this->planService->findById($plan);

        return response()->json([
            'success' => true,
            'message' => 'Plan retrieved successfully.',
            'data' => new PlanResource($planModel),
        ], 200);
    }

    /**
     * Update the specified plan.
     */
    public function update(
        UpdatePlanRequest $request,
        int $plan
    ): JsonResponse {
        $planModel = $this->planService->update(
            $plan,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Plan updated successfully.',
            'data' => new PlanResource($planModel),
        ], 200);
    }

    /**
     * Remove the specified plan.
     */
    public function destroy(int $plan): JsonResponse
    {
        $this->planService->delete($plan);

        return response()->json([
            'success' => true,
            'message' => 'Plan deleted successfully.',
            'data' => null,
        ], 200);
    }
}