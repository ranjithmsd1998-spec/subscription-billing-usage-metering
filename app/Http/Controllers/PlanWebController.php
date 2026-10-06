<?php

namespace App\Http\Controllers;

use App\Services\PlanServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\LengthAwarePaginator;

class PlanWebController extends Controller
{
    public function __construct(
        private readonly PlanServiceInterface $planService
    ) {
    }

    /**
     * Show Plan management page.
     */
    public function index()
    {
        return view('plans.index');
    }

    /**
     * Return paginated Plan list.
     */
    public function list(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);
        $search = trim((string) $request->input('search', ''));

        $perPage = in_array($perPage, [10, 25, 50, 100], true)
            ? $perPage
            : 10;

        $plans = $this->planService->getAll();

        if ($search !== '') {
            $plans = $plans->filter(function ($plan) use ($search) {
                return str_contains(
                    strtolower($plan->name ?? ''),
                    strtolower($search)
                )
                || str_contains(
                    strtolower($plan->code ?? ''),
                    strtolower($search)
                )
                || str_contains(
                    strtolower($plan->description ?? ''),
                    strtolower($search)
                );
            })->values();
        }

        $total = $plans->count();

        $items = $plans
            ->forPage($page, $perPage)
            ->values();

        $paginator = new LengthAwarePaginator(
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
            'message' => 'Plans retrieved successfully.',
            'data' => [
                'items' => $paginator->items(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * Show one Plan.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $plan = $this->planService->findById($id);

            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plan not found.',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Plan retrieved successfully.',
                'data' => $plan,
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
     * Create Plan.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'merchant_id' => ['required', 'integer', 'exists:merchants,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'currency' => ['nullable', 'string', 'size:3'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'billing_cycle' => [
                'required',
                Rule::in(['monthly', 'yearly']),
            ],
            'included_units' => ['required', 'integer', 'min:0'],
            'overage_rate' => ['required', 'numeric', 'min:0'],
            'unit_name' => ['required', 'string', 'max:100'],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        try {
            $plan = $this->planService->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Plan created successfully.',
                'data' => $plan,
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
     * Update Plan.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'merchant_id' => ['required', 'integer', 'exists:merchants,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'currency' => ['nullable', 'string', 'size:3'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'billing_cycle' => [
                'required',
                Rule::in(['monthly', 'yearly']),
            ],
            'included_units' => ['required', 'integer', 'min:0'],
            'overage_rate' => ['required', 'numeric', 'min:0'],
            'unit_name' => ['required', 'string', 'max:100'],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        try {
            $plan = $this->planService->update($id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Plan updated successfully.',
                'data' => $plan,
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
     * Delete Plan.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->planService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Plan deleted successfully.',
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