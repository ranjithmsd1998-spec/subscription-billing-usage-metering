<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Merchant\StoreMerchantRequest;
use App\Http\Requests\Merchant\UpdateMerchantRequest;
use App\Http\Resources\MerchantResource;
use App\Services\MerchantServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MerchantController extends Controller
{
    public function __construct(
        protected MerchantServiceInterface $merchantService
    ) {
    }

    /**
     * Display a paginated list of merchants.
     */
    public function index(): JsonResponse
    {
        $merchants = $this->merchantService->paginate();

        return response()->json([
            'success' => true,
            'message' => 'Merchants retrieved successfully.',
            'data' => MerchantResource::collection($merchants),
        ], 200);
    }

    /**
     * Store a newly created merchant.
     */
    public function store(
            StoreMerchantRequest $request
        ): JsonResponse {
        $merchant = $this->merchantService->create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Merchant created successfully.',
            'data' => new MerchantResource($merchant),
        ], 201);
    }

    /**
     * Display the specified merchant.
     */
    public function show(int $merchant): JsonResponse
    {
        $merchantModel = $this->merchantService->findById($merchant);

        return response()->json([
            'success' => true,
            'message' => 'Merchant retrieved successfully.',
            'data' => new MerchantResource($merchantModel),
        ], 200);
    }

    /**
     * Update the specified merchant.
     */
    public function update(
        UpdateMerchantRequest $request,
        int $merchant
    ): JsonResponse {
        $merchantModel = $this->merchantService->update(
            $merchant,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Merchant updated successfully.',
            'data' => new MerchantResource($merchantModel),
        ], 200);
    }

    /**
     * Remove the specified merchant.
     */
    public function destroy(int $merchant): JsonResponse
    {
        $this->merchantService->delete($merchant);

        return response()->json([
            'success' => true,
            'message' => 'Merchant deleted successfully.',
            'data' => null,
        ], 200);
    }
}
