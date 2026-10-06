<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Services\CustomerServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerWebController extends Controller
{
    public function __construct(
        private readonly CustomerServiceInterface $customerService
    ) {
    }

    /**
     * Display customers page.
     */
    public function index()
    {
        return view('customers.index');
    }

    /**
     * Return customers for AJAX listing.
     */
    public function list(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);
        $search = trim((string) $request->input('search', ''));
        $merchantId = $request->input('merchant_id');

        $perPage = in_array(
            $perPage,
            [10, 25, 50, 100],
            true
        )
            ? $perPage
            : 10;

        $page = max($page, 1);

        try {
            /*
             * CustomerService currently exposes paginate().
             *
             * We load a larger set for the UI search/filter
             * and then paginate the filtered collection.
             */
            $paginator = $this->customerService->paginate(100);

            $customers = collect($paginator->items());

            /*
             * Filter by merchant when merchant_id is supplied.
             *
             * This is used by Usage Events:
             *
             * /customers/data?merchant_id=1&per_page=100
             */
            if (
                $merchantId !== null &&
                $merchantId !== ''
            ) {
                $customers = $customers
                    ->filter(function ($customer) use ($merchantId) {
                        return (int) $customer->merchant_id ===
                            (int) $merchantId;
                    })
                    ->values();
            }

            /*
             * Search filter.
             */
            if ($search !== '') {
                $searchLower = strtolower($search);

                $customers = $customers
                    ->filter(function ($customer) use ($searchLower) {
                        $name = strtolower(
                            $customer->name ?? ''
                        );

                        $code = strtolower(
                            $customer->code ?? ''
                        );

                        $email = strtolower(
                            $customer->email ?? ''
                        );

                        $phone = strtolower(
                            $customer->phone ?? ''
                        );

                        $status = strtolower(
                            $customer->status ?? ''
                        );

                        $merchantName = strtolower(
                            $customer->merchant?->name ?? ''
                        );

                        $merchantCode = strtolower(
                            $customer->merchant?->code ?? ''
                        );

                        return str_contains(
                            $name,
                            $searchLower
                        )
                        || str_contains(
                            $code,
                            $searchLower
                        )
                        || str_contains(
                            $email,
                            $searchLower
                        )
                        || str_contains(
                            $phone,
                            $searchLower
                        )
                        || str_contains(
                            $status,
                            $searchLower
                        )
                        || str_contains(
                            $merchantName,
                            $searchLower
                        )
                        || str_contains(
                            $merchantCode,
                            $searchLower
                        );
                    })
                    ->values();
            }

            $total = $customers->count();

            $items = $customers
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
                'message' => 'Customers retrieved successfully.',
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
     * Show a single customer.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $customer = $this->customerService->findById($id);

            return response()->json([
                'success' => true,
                'message' => 'Customer retrieved successfully.',
                'data' => $customer,
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
     * Create customer.
     */
    public function store(
        StoreCustomerRequest $request
    ): JsonResponse {
        try {
            $customer = $this->customerService->create(
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully.',
                'data' => $customer,
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
     * Update customer.
     */
    public function update(
        UpdateCustomerRequest $request,
        int $customer
    ): JsonResponse {
        try {
            $updatedCustomer = $this->customerService->update(
                $customer,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully.',
                'data' => $updatedCustomer,
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
     * Delete customer.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->customerService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully.',
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