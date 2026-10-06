<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Repositories\PaymentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService implements PaymentServiceInterface
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository
    ) {
    }

    public function create(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $invoice = Invoice::with([
                'merchant',
                'customer',
            ])->find($data['invoice_id']);

            if (!$invoice) {
                throw ValidationException::withMessages([
                    'invoice_id' => ['Invoice not found.'],
                ]);
            }

            if ($invoice->status !== 'issued') {
                throw ValidationException::withMessages([
                    'invoice_id' => [
                        'Payment can only be created for an issued invoice.',
                    ],
                ]);
            }

            if ((int) $invoice->merchant_id !== (int) $data['merchant_id']) {
                throw ValidationException::withMessages([
                    'merchant_id' => [
                        'Merchant does not belong to the invoice.',
                    ],
                ]);
            }

            if ((int) $invoice->customer_id !== (int) $data['customer_id']) {
                throw ValidationException::withMessages([
                    'customer_id' => [
                        'Customer does not belong to the invoice.',
                    ],
                ]);
            }

            if ((float) $data['amount'] <= 0) {
                throw ValidationException::withMessages([
                    'amount' => [
                        'Payment amount must be greater than zero.',
                    ],
                ]);
            }

            if (strtoupper($data['currency']) !== strtoupper($invoice->currency)) {
                throw ValidationException::withMessages([
                    'currency' => [
                        'Payment currency must match invoice currency.',
                    ],
                ]);
            }

            $existingPayment = $this->paymentRepository->findByReference(
                (int) $data['merchant_id'],
                $data['payment_reference']
            );

            if ($existingPayment) {
                throw ValidationException::withMessages([
                    'payment_reference' => [
                        'Payment reference already exists.',
                    ],
                ]);
            }

            $completedAmount = $this->getCompletedPaymentAmount(
                (int) $invoice->id
            );

            if (
                ($completedAmount + (float) $data['amount'])
                > (float) $invoice->total
            ) {
                throw ValidationException::withMessages([
                    'amount' => [
                        'Payment amount exceeds the remaining invoice amount.',
                    ],
                ]);
            }

            if (($data['status'] ?? 'pending') === 'completed') {
                $data['paid_at'] = now();
            }

            $payment = $this->paymentRepository->create($data);

            if ($payment->status === 'completed') {
                $this->markInvoiceAsPaidIfFullyPaid($invoice);
            }

            return $this->paymentRepository->findById($payment->id);
        });
    }

    public function findById(int $id): Payment
    {
        $payment = $this->paymentRepository->findById($id);

        if (!$payment) {
            throw ValidationException::withMessages([
                'payment' => ['Payment not found.'],
            ]);
        }

        return $payment;
    }

    public function getByInvoice(int $invoiceId): Collection
    {
        return $this->paymentRepository->getByInvoice($invoiceId);
    }

    public function getByCustomer(int $customerId): Collection
    {
        return $this->paymentRepository->getByCustomer($customerId);
    }

    public function getByMerchant(int $merchantId): Collection
    {
        return $this->paymentRepository->getByMerchant($merchantId);
    }

    public function updateStatus(int $id, string $status): Payment
    {
        return DB::transaction(function () use ($id, $status) {
            $payment = $this->findById($id);

            if ($payment->status === 'refunded') {
                throw ValidationException::withMessages([
                    'status' => [
                        'A refunded payment cannot be updated.',
                    ],
                ]);
            }

            if ($payment->status === 'completed' && $status === 'pending') {
                throw ValidationException::withMessages([
                    'status' => [
                        'A completed payment cannot be changed back to pending.',
                    ],
                ]);
            }

            if ($payment->status === 'completed' && $status === 'failed') {
                throw ValidationException::withMessages([
                    'status' => [
                        'A completed payment cannot be marked as failed.',
                    ],
                ]);
            }

            $data = [
                'status' => $status,
            ];

            if ($status === 'completed') {
                $invoice = Invoice::find($payment->invoice_id);

                if (!$invoice) {
                    throw ValidationException::withMessages([
                        'invoice_id' => ['Invoice not found.'],
                    ]);
                }

                if ($invoice->status !== 'issued') {
                    throw ValidationException::withMessages([
                        'status' => [
                            'Payment can only be completed for an issued invoice.',
                        ],
                    ]);
                }

                $otherCompletedAmount = $this->getCompletedPaymentAmount(
                    (int) $payment->invoice_id,
                    (int) $payment->id
                );

                if (
                    $otherCompletedAmount + (float) $payment->amount
                    > (float) $invoice->total
                ) {
                    throw ValidationException::withMessages([
                        'status' => [
                            'Completing this payment would exceed the invoice total.',
                        ],
                    ]);
                }

                $data['paid_at'] = now();
            }

            if ($status !== 'completed') {
                $data['paid_at'] = null;
            }

            $payment = $this->paymentRepository->update(
                $payment,
                $data
            );

            if ($status === 'completed') {
                $invoice = Invoice::find($payment->invoice_id);

                if ($invoice) {
                    $this->markInvoiceAsPaidIfFullyPaid($invoice);
                }
            }

            return $payment;
        });
    }

    public function refund(int $id): Payment
    {
        return DB::transaction(function () use ($id) {
            $payment = $this->findById($id);

            if ($payment->status !== 'completed') {
                throw ValidationException::withMessages([
                    'payment' => [
                        'Only completed payments can be refunded.',
                    ],
                ]);
            }

            $payment = $this->paymentRepository->update(
                $payment,
                [
                    'status' => 'refunded',
                ]
            );

            $invoice = Invoice::find($payment->invoice_id);

            if ($invoice && $invoice->status === 'paid') {
                $invoice->update([
                    'status' => 'issued',
                    'paid_at' => null,
                ]);
            }

            return $payment;
        });
    }

    private function getCompletedPaymentAmount(
        int $invoiceId,
        ?int $excludePaymentId = null
    ): float {
        $query = Payment::where('invoice_id', $invoiceId)
            ->where('status', 'completed');

        if ($excludePaymentId !== null) {
            $query->where('id', '!=', $excludePaymentId);
        }

        return (float) $query->sum('amount');
    }

    private function markInvoiceAsPaidIfFullyPaid(
        Invoice $invoice
    ): void {
        $completedAmount = $this->getCompletedPaymentAmount(
            (int) $invoice->id
        );

        if ($completedAmount >= (float) $invoice->total) {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        }
    }
}