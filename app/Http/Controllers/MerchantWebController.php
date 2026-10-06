<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Services\MerchantServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class MerchantWebController extends Controller
{
    public function __construct(
        private readonly MerchantServiceInterface $merchantService
    ) {
    }

    /**
     * Display merchant management page.
     */
    public function index(): View
    {
        return view('merchants.index');
    }

    /**
     * Return merchants for AJAX listing.
     */
    public function list(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'in:10,25,50,100'],
        ]);

        $search = trim($validated['search'] ?? '');

        $page = (int) ($validated['page'] ?? 1);

        $perPage = (int) ($validated['per_page'] ?? 10);

        /*
         * Reuse the existing Merchant service.
         *
         * The existing service returns the merchant collection.
         * Pagination for the Blade screen is handled here so the
         * existing Merchant API/service implementation is not changed.
         */
        $merchants = $this->merchantService->getAll();

        if ($merchants instanceof LengthAwarePaginator) {
            return response()->json([
                'success' => true,
                'message' => 'Merchants retrieved successfully.',
                'data' => $merchants,
            ]);
        }

        if ($merchants instanceof Collection) {
            $collection = $merchants;
        } else {
            $collection = collect($merchants);
        }

        if ($search !== '') {
            $searchLower = mb_strtolower($search);

            $collection = $collection->filter(
                function ($merchant) use ($searchLower) {
                    return str_contains(
                        mb_strtolower(
                            (string) ($merchant->name ?? '')
                        ),
                        $searchLower
                    )
                    || str_contains(
                        mb_strtolower(
                            (string) ($merchant->code ?? '')
                        ),
                        $searchLower
                    )
                    || str_contains(
                        mb_strtolower(
                            (string) ($merchant->email ?? '')
                        ),
                        $searchLower
                    );
                }
            );
        }

        $collection = $collection->values();

        $total = $collection->count();

        $items = $collection
            ->slice(($page - 1) * $perPage, $perPage)
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
            'message' => 'Merchants retrieved successfully.',
            'data' => [
                'data' => $paginator->items(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }

    /**
     * Return a single merchant for editing.
     */
    public function show(int $id): JsonResponse
    {
        $merchant = $this->merchantService->findById($id);

        if (!$merchant) {
            return response()->json([
                'success' => false,
                'message' => 'Merchant not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Merchant retrieved successfully.',
            'data' => $merchant,
        ]);
    }

    /**
     * Create merchant.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate(
            $this->validationRules()
        );

        $merchant = $this->merchantService->create(
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Merchant created successfully.',
            'data' => $merchant,
        ], 201);
    }

    /**
     * Update merchant.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('merchants', 'code')->ignore($id),
            ],
            'email' => ['required', 'email', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'timezone' => ['nullable', 'string', 'max:100'],
            'currency' => ['nullable', 'string', 'size:3'],
            'phone' => ['nullable', 'string', 'max:30'],
            'metadata' => ['nullable', 'json'],
        ]);

        try {
            $merchant = $this->merchantService->update($id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Merchant updated successfully.',
                'data' => $merchant,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete merchant.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->merchantService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Merchant deleted successfully.',
                'data' => null,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Merchant validation rules.
     */
    private function validationRules(
        ?Merchant $merchant = null
    ): array {
        $codeRule = Rule::unique('merchants', 'code');

        $emailRule = Rule::unique('merchants', 'email');

        if ($merchant) {
            $codeRule->ignore($merchant->id);

            $emailRule->ignore($merchant->id);
        }

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'required',
                'string',
                'max:255',
                $codeRule,
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                $emailRule,
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],

            'timezone' => [
                'nullable',
                'string',
                'max:100',
            ],

            'currency' => [
                'nullable',
                'string',
                'size:3',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'metadata' => [
                'nullable',
                'array',
            ],
        ];
    }
}