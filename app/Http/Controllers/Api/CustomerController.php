<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerServiceInterface;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerServiceInterface $customerService
    ) {
    }

    /**
     * Display a paginated list of customers.
     */
    public function index(): JsonResponse
    {
        $customers = $this->customerService->paginate();

        return response()->json([
            'success' => true,
            'message' => 'Customers retrieved successfully.',
            'data' => CustomerResource::collection($customers),
        ], 200);
    }

    /**
     * Store a newly created customer.
     */
    public function store(
        StoreCustomerRequest $request
    ): JsonResponse {
        $customer = $this->customerService->create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully.',
            'data' => new CustomerResource($customer),
        ], 201);
    }

    /**
     * Display the specified customer.
     */
    public function show(int $customer): JsonResponse
    {
        $customerModel = $this->customerService->findById($customer);

        return response()->json([
            'success' => true,
            'message' => 'Customer retrieved successfully.',
            'data' => new CustomerResource($customerModel),
        ], 200);
    }

    /**
     * Update the specified customer.
     */
    public function update(
        UpdateCustomerRequest $request,
        int $customer
    ): JsonResponse {
        $customerModel = $this->customerService->update(
            $customer,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully.',
            'data' => new CustomerResource($customerModel),
        ], 200);
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(int $customer): JsonResponse
    {
        $this->customerService->delete($customer);

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully.',
            'data' => null,
        ], 200);
    }
}