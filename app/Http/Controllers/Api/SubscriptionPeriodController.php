<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionPeriod\StoreSubscriptionPeriodRequest;
use App\Http\Requests\SubscriptionPeriod\UpdateSubscriptionPeriodRequest;
use App\Http\Resources\SubscriptionPeriodResource;
use App\Services\SubscriptionPeriodServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionPeriodController extends Controller
{
    public function __construct(
        private readonly SubscriptionPeriodServiceInterface $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = min(
            max((int) $request->input('per_page', 15), 1),
            100
        );

        $filters = [
            'subscription_id' => $request->input('subscription_id'),
            'plan_id' => $request->input('plan_id'),
            'billing_cycle' => $request->input('billing_cycle'),
            'starts_from' => $request->input('starts_from'),
            'starts_to' => $request->input('starts_to'),
        ];

        $periods = $this->service->getAll(
            $filters,
            $perPage
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription periods retrieved successfully.',
            'data' => SubscriptionPeriodResource::collection($periods),
            'meta' => [
                'current_page' => $periods->currentPage(),
                'last_page' => $periods->lastPage(),
                'per_page' => $periods->perPage(),
                'total' => $periods->total(),
            ],
        ]);
    }

    public function store(
        StoreSubscriptionPeriodRequest $request
    ): JsonResponse {
        $period = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription period created successfully.',
            'data' => new SubscriptionPeriodResource($period),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $period = $this->service->getById($id);

        return response()->json([
            'success' => true,
            'message' => 'Subscription period retrieved successfully.',
            'data' => new SubscriptionPeriodResource($period),
        ]);
    }

    public function update(
        UpdateSubscriptionPeriodRequest $request,
        int $id
    ): JsonResponse {
        $period = $this->service->update(
            $id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription period updated successfully.',
            'data' => new SubscriptionPeriodResource($period),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Subscription period deleted successfully.',
            'data' => null,
        ]);
    }
}