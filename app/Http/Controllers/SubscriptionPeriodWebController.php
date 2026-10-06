<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionPeriodServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SubscriptionPeriodWebController extends Controller
{
    public function __construct(
        private readonly SubscriptionPeriodServiceInterface $subscriptionPeriodService
    ) {
    }

    /**
     * Display subscription periods page.
     */
    public function index()
    {
        return view('subscription-periods.index');
    }

    /**
     * Return subscription periods for JavaScript listing.
     */
    public function list(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);
        $search = trim((string) $request->input('search', ''));

        $perPage = in_array($perPage, [10, 25, 50, 100], true)
            ? $perPage
            : 10;

        $page = max($page, 1);

        try {
            /*
             * Load subscription periods with their required
             * relationships through the service/repository.
             */
            $paginator = $this->subscriptionPeriodService->getAll(
                [],
                1000
            );

            $periods = collect($paginator->items());

            if ($search !== '') {
                $searchLower = strtolower($search);

                $periods = $periods
                    ->filter(function ($period) use ($searchLower) {
                        $customerName = strtolower(
                            $period->subscription?->customer?->name ?? ''
                        );

                        $customerCode = strtolower(
                            $period->subscription?->customer?->code ?? ''
                        );

                        $planName = strtolower(
                            $period->plan?->name ?? ''
                        );

                        $planCode = strtolower(
                            $period->plan?->code ?? ''
                        );

                        $billingCycle = strtolower(
                            $period->billing_cycle ?? ''
                        );

                        $startsAt = strtolower(
                            (string) $period->starts_at
                        );

                        $endsAt = strtolower(
                            (string) $period->ends_at
                        );

                        return str_contains($customerName, $searchLower)
                            || str_contains($customerCode, $searchLower)
                            || str_contains($planName, $searchLower)
                            || str_contains($planCode, $searchLower)
                            || str_contains($billingCycle, $searchLower)
                            || str_contains($startsAt, $searchLower)
                            || str_contains($endsAt, $searchLower);
                    })
                    ->values();
            }

            $total = $periods->count();

            $items = $periods
                ->forPage($page, $perPage)
                ->values();

            $result = new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Subscription periods retrieved successfully.',
                'data' => [
                    'items' => $result->items(),
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                    'per_page' => $result->perPage(),
                    'total' => $result->total(),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 422);
        }
    }

    /**
     * Show a single subscription period.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $period = $this->subscriptionPeriodService->getById($id);

            return response()->json([
                'success' => true,
                'message' => 'Subscription period retrieved successfully.',
                'data' => $period,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 404);
        }
    }

    /**
     * Create subscription period.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subscription_id' => [
                'required',
                'integer',
                'exists:subscriptions,id',
            ],
            'starts_at' => [
                'required',
                'date',
            ],
            'ends_at' => [
                'nullable',
                'date',
                'after:starts_at',
            ],
        ]);

        try {
            $period = $this->subscriptionPeriodService->create(
                $validated
            );

            return response()->json([
                'success' => true,
                'message' => 'Subscription period created successfully.',
                'data' => $period,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 422);
        }
    }

    /**
     * Update subscription period.
     *
     * Only starts_at and ends_at can be changed.
     */
    public function update(
        Request $request,
        int $id
    ): JsonResponse {
        $validated = $request->validate([
            'starts_at' => [
                'sometimes',
                'required',
                'date',
            ],
            'ends_at' => [
                'sometimes',
                'nullable',
                'date',
                'after:starts_at',
            ],
        ]);

        try {
            $period = $this->subscriptionPeriodService->update(
                $id,
                $validated
            );

            return response()->json([
                'success' => true,
                'message' => 'Subscription period updated successfully.',
                'data' => $period,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 422);
        }
    }

    /**
     * Delete subscription period.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->subscriptionPeriodService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Subscription period deleted successfully.',
                'data' => null,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 422);
        }
    }
}