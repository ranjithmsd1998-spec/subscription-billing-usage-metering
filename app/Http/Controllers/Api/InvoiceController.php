<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\GenerateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Services\InvoiceServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceServiceInterface $service
    ) {
    }

    /**
     * Generate an invoice for a subscription period.
     */
    public function generate(
        GenerateInvoiceRequest $request
    ): JsonResponse {
        $invoice = $this->service->generate(
            $request->integer('subscription_period_id'),
            (float) ($request->input('tax_rate', 0)),
            $request->input('due_at')
        );

        return response()->json([
            'success' => true,
            'message' => 'Invoice generated successfully.',
            'data' => new InvoiceResource($invoice),
        ], 201);
    }

    /**
     * Get a single invoice.
     */
    public function show(int $id): JsonResponse
    {
        $invoice = $this->service->findById($id);

        return response()->json([
            'success' => true,
            'message' => 'Invoice retrieved successfully.',
            'data' => new InvoiceResource($invoice),
        ]);
    }

    /**
     * Get invoices for a customer.
     */
    public function customerInvoices(
        Request $request
    ): JsonResponse {
        $request->validate([
            'customer_id' => [
                'required',
                'integer',
                'exists:customers,id',
            ],
        ]);

        $invoices = $this->service->getByCustomer(
            $request->integer('customer_id')
        );

        return response()->json([
            'success' => true,
            'message' => 'Customer invoices retrieved successfully.',
            'data' => InvoiceResource::collection($invoices),
        ]);
    }

    /**
     * Get invoices for a merchant.
     */
    public function merchantInvoices(
        Request $request
    ): JsonResponse {
        $request->validate([
            'merchant_id' => [
                'required',
                'integer',
                'exists:merchants,id',
            ],
        ]);

        $invoices = $this->service->getByMerchant(
            $request->integer('merchant_id')
        );

        return response()->json([
            'success' => true,
            'message' => 'Merchant invoices retrieved successfully.',
            'data' => InvoiceResource::collection($invoices),
        ]);
    }

    /**
     * Issue an invoice.
     */
    public function issue(int $id): JsonResponse
    {
        $invoice = $this->service->issue($id);

        return response()->json([
            'success' => true,
            'message' => 'Invoice issued successfully.',
            'data' => new InvoiceResource($invoice),
        ]);
    }

    /**
     * Mark an invoice as paid.
     */
    public function pay(int $id): JsonResponse
    {
        $invoice = $this->service->markAsPaid($id);

        return response()->json([
            'success' => true,
            'message' => 'Invoice marked as paid successfully.',
            'data' => new InvoiceResource($invoice),
        ]);
    }

    /**
     * Void an invoice.
     */
    public function void(int $id): JsonResponse
    {
        $invoice = $this->service->void($id);

        return response()->json([
            'success' => true,
            'message' => 'Invoice voided successfully.',
            'data' => new InvoiceResource($invoice),
        ]);
    }
}