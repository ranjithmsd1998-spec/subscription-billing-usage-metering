<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyUsageAggregate\GenerateDailyUsageAggregateRequest;
use App\Http\Resources\DailyUsageAggregateResource;
use App\Services\DailyUsageAggregateServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailyUsageAggregateController extends Controller
{
    public function __construct(
        private DailyUsageAggregateServiceInterface $service
    ) {
    }

    /**
     * Generate or recalculate a daily usage aggregate.
     */
    public function generate(
        GenerateDailyUsageAggregateRequest $request
    ): JsonResponse {
        $aggregate = $this->service->generate(
            $request->integer('subscription_period_id'),
            $request->string('usage_date')->toString()
        );

        return response()->json([
            'success' => true,
            'message' => 'Daily usage aggregate generated successfully.',
            'data' => new DailyUsageAggregateResource($aggregate),
        ], 200);
    }

    /**
     * Get a single aggregate.
     */
    public function show(int $id): JsonResponse
    {
        $aggregate = $this->service->findById($id);

        return response()->json([
            'success' => true,
            'message' => 'Daily usage aggregate retrieved successfully.',
            'data' => new DailyUsageAggregateResource($aggregate),
        ]);
    }

    /**
     * Get all aggregates for a subscription period.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'subscription_period_id' => [
                'required',
                'integer',
                'exists:subscription_periods,id',
            ],
        ]);

        $aggregates = $this->service->getBySubscriptionPeriod(
            $request->integer('subscription_period_id')
        );

        return response()->json([
            'success' => true,
            'message' => 'Daily usage aggregates retrieved successfully.',
            'data' => DailyUsageAggregateResource::collection($aggregates),
        ]);
    }
}