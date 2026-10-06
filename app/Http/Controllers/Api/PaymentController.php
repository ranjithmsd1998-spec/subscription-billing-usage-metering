<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Requests\Payment\UpdatePaymentStatusRequest;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentServiceInterface $paymentService
    ) {
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $payment = $this->paymentService->create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Payment created successfully.',
            'data' => new PaymentResource($payment),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $payment = $this->paymentService->findById($id);

        return response()->json([
            'success' => true,
            'message' => 'Payment retrieved successfully.',
            'data' => new PaymentResource($payment),
        ]);
    }

    public function byInvoice(Request $request): JsonResponse
    {
        $request->validate([
            'invoice_id' => [
                'required',
                'integer',
                'exists:invoices,id',
            ],
        ]);

        $payments = $this->paymentService->getByInvoice(
            (int) $request->invoice_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Invoice payments retrieved successfully.',
            'data' => PaymentResource::collection($payments),
        ]);
    }

    public function byCustomer(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => [
                'required',
                'integer',
                'exists:customers,id',
            ],
        ]);

        $payments = $this->paymentService->getByCustomer(
            (int) $request->customer_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Customer payments retrieved successfully.',
            'data' => PaymentResource::collection($payments),
        ]);
    }

    public function byMerchant(Request $request): JsonResponse
    {
        $request->validate([
            'merchant_id' => [
                'required',
                'integer',
                'exists:merchants,id',
            ],
        ]);

        $payments = $this->paymentService->getByMerchant(
            (int) $request->merchant_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Merchant payments retrieved successfully.',
            'data' => PaymentResource::collection($payments),
        ]);
    }

    public function updateStatus(
        UpdatePaymentStatusRequest $request,
        int $id
    ): JsonResponse {
        $payment = $this->paymentService->updateStatus(
            $id,
            $request->validated('status')
        );

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully.',
            'data' => new PaymentResource($payment),
        ]);
    }

    public function refund(int $id): JsonResponse
    {
        $payment = $this->paymentService->refund($id);

        return response()->json([
            'success' => true,
            'message' => 'Payment refunded successfully.',
            'data' => new PaymentResource($payment),
        ]);
    }
}