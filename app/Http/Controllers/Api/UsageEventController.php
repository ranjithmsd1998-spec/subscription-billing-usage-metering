<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsageEvent\StoreUsageEventRequest;
use App\Http\Requests\UsageEvent\UpdateUsageEventRequest;
use App\Http\Resources\UsageEventResource;
use App\Services\UsageEventServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsageEventController extends Controller
{
    public function __construct(
        private readonly UsageEventServiceInterface $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = min(
            max((int) $request->input('per_page', 15), 1),
            100
        );

        $filters = [
            'merchant_id' => $request->input('merchant_id'),
            'customer_id' => $request->input('customer_id'),
            'subscription_id' => $request->input('subscription_id'),
            'subscription_period_id' => $request->input(
                'subscription_period_id'
            ),
            'unit_name' => $request->input('unit_name'),
            'occurred_from' => $request->input('occurred_from'),
            'occurred_to' => $request->input('occurred_to'),
        ];

        $usageEvents = $this->service->getAll(
            $filters,
            $perPage
        );

        return response()->json([
            'success' => true,
            'message' => 'Usage events retrieved successfully.',
            'data' => UsageEventResource::collection(
                $usageEvents
            ),
            'meta' => [
                'current_page' => $usageEvents->currentPage(),
                'last_page' => $usageEvents->lastPage(),
                'per_page' => $usageEvents->perPage(),
                'total' => $usageEvents->total(),
            ],
        ]);
    }

    public function store(
        StoreUsageEventRequest $request
    ): JsonResponse {
        $usageEvent = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Usage event created successfully.',
            'data' => new UsageEventResource($usageEvent),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $usageEvent = $this->service->getById($id);

        return response()->json([
            'success' => true,
            'message' => 'Usage event retrieved successfully.',
            'data' => new UsageEventResource($usageEvent),
        ]);
    }

    public function update(
        UpdateUsageEventRequest $request,
        int $id
    ): JsonResponse {
        $usageEvent = $this->service->update(
            $id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Usage event updated successfully.',
            'data' => new UsageEventResource($usageEvent),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Usage event deleted successfully.',
            'data' => null,
        ]);
    }
}