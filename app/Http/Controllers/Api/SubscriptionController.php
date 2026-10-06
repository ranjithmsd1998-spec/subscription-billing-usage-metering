<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\StoreSubscriptionRequest;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Services\SubscriptionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly SubscriptionServiceInterface $subscriptionService
    ) {
    }

    /**
     * Display a paginated list of subscriptions.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'merchant_id' => $request->input('merchant_id'),
            'customer_id' => $request->input('customer_id'),
            'plan_id' => $request->input('plan_id'),
            'status' => $request->input('status'),
            'per_page' => $request->input('per_page', 15),
        ];

        $subscriptions = $this->subscriptionService
            ->getAll($filters);

        return response()->json([
            'success' => true,
            'message' => 'Subscriptions retrieved successfully.',
            'data' => SubscriptionResource::collection($subscriptions),
            'meta' => [
                'current_page' => $subscriptions->currentPage(),
                'last_page' => $subscriptions->lastPage(),
                'per_page' => $subscriptions->perPage(),
                'total' => $subscriptions->total(),
            ],
        ]);
    }

    /**
     * Store a newly created subscription.
     */
    public function store(
        StoreSubscriptionRequest $request
    ): JsonResponse {
        $subscription = $this->subscriptionService->create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription created successfully.',
            'data' => new SubscriptionResource($subscription),
        ], 201);
    }

    /**
     * Display the specified subscription.
     */
    public function show(int $id): JsonResponse
    {
        $subscription = $this->subscriptionService->getById($id);

        return response()->json([
            'success' => true,
            'message' => 'Subscription retrieved successfully.',
            'data' => new SubscriptionResource($subscription),
        ]);
    }

    /**
     * Update the specified subscription.
     */
    public function update(
        UpdateSubscriptionRequest $request,
        int $id
    ): JsonResponse {
        $subscription = $this->subscriptionService->update(
            $id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription updated successfully.',
            'data' => new SubscriptionResource($subscription),
        ]);
    }

    /**
     * Remove the specified subscription.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->subscriptionService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Subscription deleted successfully.',
            'data' => null,
        ]);
    }
}