<?php

namespace App\Http\Controllers;

use App\Services\UsageEventServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UsageEventWebController extends Controller
{
    public function __construct(
        private readonly UsageEventServiceInterface $usageEventService
    ) {
    }

    public function index()
    {
        return view('usage-events.index');
    }

    public function list(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        /*
         * Fetch enough records for web-side filtering/pagination.
         * Existing repository/service already handles eager loading.
         */
        $paginator = $this->usageEventService->getAll([], 1000);

        $items = collect($paginator->items());

        $search = trim((string) $request->input('search', ''));

        if ($search !== '') {
            $searchLower = strtolower($search);

            $items = $items->filter(function ($event) use ($searchLower) {
                $customerName = strtolower(
                    (string) ($event->customer?->name ?? '')
                );

                $customerCode = strtolower(
                    (string) ($event->customer?->code ?? '')
                );

                $subscriptionId = strtolower(
                    (string) ($event->subscription_id ?? '')
                );

                $periodId = strtolower(
                    (string) ($event->subscription_period_id ?? '')
                );

                $idempotencyKey = strtolower(
                    (string) ($event->idempotency_key ?? '')
                );

                $unitName = strtolower(
                    (string) ($event->unit_name ?? '')
                );

                return str_contains($customerName, $searchLower)
                    || str_contains($customerCode, $searchLower)
                    || str_contains($subscriptionId, $searchLower)
                    || str_contains($periodId, $searchLower)
                    || str_contains($idempotencyKey, $searchLower)
                    || str_contains($unitName, $searchLower);
            })->values();
        }

        $page = max(
            (int) $request->input('page', 1),
            1
        );

        $total = $items->count();

        $pageItems = $items
            ->forPage($page, $perPage)
            ->values();

        $paginated = new LengthAwarePaginator(
            $pageItems,
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
            'message' => 'Usage events retrieved successfully.',
            'data' => [
                'items' => $paginated->items(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $usageEvent = $this->usageEventService->getById($id);

        return response()->json([
            'success' => true,
            'message' => 'Usage event retrieved successfully.',
            'data' => $usageEvent,
        ]);
    }

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

            'subscription_id' => [
                'required',
                'integer',
                'exists:subscriptions,id',
            ],

            'subscription_period_id' => [
                'required',
                'integer',
                'exists:subscription_periods,id',
            ],

            'idempotency_key' => [
                'required',
                'string',
                'max:255',
            ],

            'usage_units' => [
                'required',
                'integer',
                'min:0',
            ],

            'unit_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'occurred_at' => [
                'required',
                'date',
            ],

            'metadata' => [
                'nullable',
                'array',
            ],
        ]);

        $usageEvent = $this->usageEventService->create(
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Usage event created successfully.',
            'data' => $usageEvent,
        ], 201);
    }

    public function update(
        Request $request,
        int $id
    ): JsonResponse {
        /*
         * Identity fields cannot be changed:
         *
         * merchant_id
         * customer_id
         * subscription_id
         * subscription_period_id
         * idempotency_key
         *
         * Only usage-related mutable fields are accepted.
         */
        $validated = $request->validate([
            'usage_units' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
            ],

            'unit_name' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],

            'occurred_at' => [
                'sometimes',
                'required',
                'date',
            ],

            'metadata' => [
                'sometimes',
                'nullable',
                'array',
            ],
        ]);

        $usageEvent = $this->usageEventService->update(
            $id,
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Usage event updated successfully.',
            'data' => $usageEvent,
        ]);
    }
}