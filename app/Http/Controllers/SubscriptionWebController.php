<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;

class SubscriptionWebController extends Controller
{
    public function __construct(
        private readonly SubscriptionServiceInterface $subscriptionService
    ) {
    }

    /**
     * Display subscriptions page.
     */
    public function index()
    {
        return view('subscriptions.index');
    }

    /**
     * Return subscriptions for JavaScript listing.
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
            $paginator = $this->subscriptionService->getAll();

            $subscriptions = collect($paginator->items());

            if ($search !== '') {
                $searchLower = strtolower($search);

                $subscriptions = $subscriptions
                    ->filter(function ($subscription) use ($searchLower) {
                        $customerName = strtolower(
                            $subscription->customer?->name ?? ''
                        );

                        $customerCode = strtolower(
                            $subscription->customer?->code ?? ''
                        );

                        $planName = strtolower(
                            $subscription->plan?->name ?? ''
                        );

                        $planCode = strtolower(
                            $subscription->plan?->code ?? ''
                        );

                        $status = strtolower(
                            $subscription->status ?? ''
                        );

                        return str_contains(
                            $customerName,
                            $searchLower
                        )
                        || str_contains(
                            $customerCode,
                            $searchLower
                        )
                        || str_contains(
                            $planName,
                            $searchLower
                        )
                        || str_contains(
                            $planCode,
                            $searchLower
                        )
                        || str_contains(
                            $status,
                            $searchLower
                        );
                    })
                    ->values();
            }

            /*
             * Since search is performed on the loaded result,
             * paginate the filtered collection for the UI.
             */
            $total = $subscriptions->count();

            $items = $subscriptions
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
                'message' => 'Subscriptions retrieved successfully.',
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
     * Show a single subscription.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $subscription =
                $this->subscriptionService->findById($id);

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription not found.',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Subscription retrieved successfully.',
                'data' => $subscription,
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
     * Create subscription.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'merchant_id' => [
                'required',
                'integer',
                'exists:merchants,id',
            ],

            'customer_id' => [
                'required',
                'integer',
                'exists:customers,id',
            ],

            'plan_id' => [
                'required',
                'integer',
                'exists:plans,id',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'active',
                    'cancelled',
                    'expired',
                ]),
            ],

            'started_at' => [
                'required',
                'date',
            ],

            'current_period_start' => [
                'required',
                'date',
            ],

            'current_period_end' => [
                'required',
                'date',
                'after_or_equal:current_period_start',
            ],

            'cancelled_at' => [
                'nullable',
                'date',
                'after_or_equal:started_at',
            ],
        ]);

        try {
            $subscription =
                $this->subscriptionService->create(
                    $validated
                );

            return response()->json([
                'success' => true,
                'message' => 'Subscription created successfully.',
                'data' => $subscription,
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
     * Update subscription.
     */
    public function update(
        Request $request,
        int $id
    ): JsonResponse {
        $validated = $request->validate([
            'merchant_id' => [
                'required',
                'integer',
                'exists:merchants,id',
            ],

            'customer_id' => [
                'required',
                'integer',
                'exists:customers,id',
            ],

            'plan_id' => [
                'required',
                'integer',
                'exists:plans,id',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'cancelled',
                    'expired',
                ]),
            ],

            'started_at' => [
                'required',
                'date',
            ],

            'current_period_start' => [
                'required',
                'date',
            ],

            'current_period_end' => [
                'required',
                'date',
                'after_or_equal:current_period_start',
            ],

            'cancelled_at' => [
                'nullable',
                'date',
                'after_or_equal:started_at',
            ],
        ]);

        try {
            $subscription =
                $this->subscriptionService->update(
                    $id,
                    $validated
                );

            return response()->json([
                'success' => true,
                'message' => 'Subscription updated successfully.',
                'data' => $subscription,
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
     * Delete subscription.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->subscriptionService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Subscription deleted successfully.',
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