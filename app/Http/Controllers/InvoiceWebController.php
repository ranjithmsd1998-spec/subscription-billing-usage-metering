<?php

namespace App\Http\Controllers;

use App\Services\InvoiceServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class InvoiceWebController extends Controller
{
    public function __construct(
        private readonly InvoiceServiceInterface $invoiceService
    ) {
    }

    public function index(): View
    {
        return view('invoices.index');
    }

    public function list(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $merchantId = $request->integer('merchant_id');
        $customerId = $request->integer('customer_id');
        $status = trim((string) $request->input('status', ''));
        $search = strtolower(trim((string) $request->input('search', '')));

        if ($merchantId > 0) {
            $invoices = $this->invoiceService->getByMerchant($merchantId);
        } elseif ($customerId > 0) {
            $invoices = $this->invoiceService->getByCustomer($customerId);
        } else {
            $invoices = collect();
        }

        $filtered = $invoices->filter(function ($invoice) use (
            $status,
            $search
        ) {
            if ($status !== '' && $invoice->status !== $status) {
                return false;
            }

            if ($search === '') {
                return true;
            }

            $values = [
                $invoice->invoice_number,
                $invoice->status,
                $invoice->currency,
                optional($invoice->customer)->name,
                optional($invoice->customer)->code,
                optional($invoice->subscription)->id,
            ];

            foreach ($values as $value) {
                if (
                    $value !== null &&
                    str_contains(
                        strtolower((string) $value),
                        $search
                    )
                ) {
                    return true;
                }
            }

            return false;
        })->values();

        $page = max((int) $request->input('page', 1), 1);

        $paginator = new LengthAwarePaginator(
            $filtered->forPage($page, $perPage)->values(),
            $filtered->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Invoices retrieved successfully.',
            'data' => [
                'items' => $paginator->items(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $invoice = $this->invoiceService->findById($id);

        return response()->json([
            'success' => true,
            'message' => 'Invoice retrieved successfully.',
            'data' => $invoice,
        ]);
    }

    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subscription_period_id' => [
                'required',
                'integer',
                'exists:subscription_periods,id',
            ],
            'tax_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'due_at' => [
                'nullable',
                'date',
            ],
        ]);

        $invoice = $this->invoiceService->generate(
            (int) $validated['subscription_period_id'],
            (float) ($validated['tax_rate'] ?? 0),
            $validated['due_at'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Invoice generated successfully.',
            'data' => $invoice,
        ], 201);
    }

    public function issue(int $id): JsonResponse
    {
        $invoice = $this->invoiceService->issue($id);

        return response()->json([
            'success' => true,
            'message' => 'Invoice issued successfully.',
            'data' => $invoice,
        ]);
    }

    public function pay(int $id): JsonResponse
    {
        $invoice = $this->invoiceService->markAsPaid($id);

        return response()->json([
            'success' => true,
            'message' => 'Invoice marked as paid successfully.',
            'data' => $invoice,
        ]);
    }

    public function void(int $id): JsonResponse
    {
        $invoice = $this->invoiceService->void($id);

        return response()->json([
            'success' => true,
            'message' => 'Invoice voided successfully.',
            'data' => $invoice,
        ]);
    }
}