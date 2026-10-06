<?php

namespace App\Http\Controllers;

use App\Services\PaymentServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentWebController extends Controller
{
    public function __construct(
        private readonly PaymentServiceInterface $paymentService
    ) {
    }

    public function list(Request $request): JsonResponse
    {
        $request->validate([
            'invoice_id' => ['nullable', 'integer', 'exists:invoices,id'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'merchant_id' => ['nullable', 'integer', 'exists:merchants,id'],
        ]);

        if ($request->filled('invoice_id')) {
            $payments = $this->paymentService->getByInvoice(
                (int) $request->input('invoice_id')
            );
        } elseif ($request->filled('customer_id')) {
            $payments = $this->paymentService->getByCustomer(
                (int) $request->input('customer_id')
            );
        } elseif ($request->filled('merchant_id')) {
            $payments = $this->paymentService->getByMerchant(
                (int) $request->input('merchant_id')
            );
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Payments retrieved successfully.',
                'data' => [],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payments retrieved successfully.',
            'data' => $payments,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $payment = $this->paymentService->findById($id);

        return response()->json([
            'success' => true,
            'message' => 'Payment retrieved successfully.',
            'data' => $payment,
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
            'invoice_id' => [
                'required',
                'integer',
                'exists:invoices,id',
            ],
            'payment_reference' => [
                'required',
                'string',
                'max:255',
            ],
            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'currency' => [
                'required',
                'string',
                'size:3',
            ],
            'payment_method' => [
                'required',
                'string',
                'max:100',
            ],
            'status' => [
                'required',
                'in:pending,completed',
            ],
            'metadata' => [
                'nullable',
                'array',
            ],
        ]);

        $payment = $this->paymentService->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Payment created successfully.',
            'data' => $payment,
        ], 201);
    }

    public function updateStatus(
        Request $request,
        int $id
    ): JsonResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:pending,completed,failed',
            ],
        ]);

        $payment = $this->paymentService->updateStatus(
            $id,
            $validated['status']
        );

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully.',
            'data' => $payment,
        ]);
    }

    public function refund(int $id): JsonResponse
    {
        $payment = $this->paymentService->refund($id);

        return response()->json([
            'success' => true,
            'message' => 'Payment refunded successfully.',
            'data' => $payment,
        ]);
    }
}