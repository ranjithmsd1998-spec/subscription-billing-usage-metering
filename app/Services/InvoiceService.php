<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\SubscriptionPeriod;
use App\Repositories\InvoiceRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceService implements InvoiceServiceInterface
{
    public function __construct(
        private InvoiceRepositoryInterface $repository
    ) {
    }

    /**
     * Generate an invoice for a subscription period.
     */
    public function generate(
        int $subscriptionPeriodId,
        float $taxRate = 0,
        ?string $dueAt = null
    ): Invoice {
        return DB::transaction(function () use (
            $subscriptionPeriodId,
            $taxRate,
            $dueAt
        ) {
            /*
             * ---------------------------------------------------------
             * TAX VALIDATION
             * ---------------------------------------------------------
             */
            if ($taxRate < 0 || $taxRate > 100) {
                throw ValidationException::withMessages([
                    'tax_rate' => [
                        'Tax rate must be between 0 and 100.',
                    ],
                ]);
            }

            /*
             * ---------------------------------------------------------
             * LOAD SUBSCRIPTION PERIOD
             * ---------------------------------------------------------
             */
            $period = SubscriptionPeriod::query()
                ->with([
                    'subscription',
                    'subscription.merchant',
                    'subscription.customer',
                    'plan',
                ])
                ->find($subscriptionPeriodId);

            if (!$period) {
                throw ValidationException::withMessages([
                    'subscription_period_id' => [
                        'The selected subscription period does not exist.',
                    ],
                ]);
            }

            /*
             * ---------------------------------------------------------
             * VALIDATE RELATIONSHIPS
             * ---------------------------------------------------------
             */
            if (!$period->subscription) {
                throw ValidationException::withMessages([
                    'subscription_period_id' => [
                        'The subscription period does not have a valid subscription.',
                    ],
                ]);
            }

            if (!$period->plan) {
                throw ValidationException::withMessages([
                    'subscription_period_id' => [
                        'The subscription period does not have a valid plan.',
                    ],
                ]);
            }

            /*
             * ---------------------------------------------------------
             * DUPLICATE INVOICE CHECK
             * ---------------------------------------------------------
             */
            $existingInvoice = $this->repository
                ->findBySubscriptionPeriod($subscriptionPeriodId);

            if ($existingInvoice) {
                throw ValidationException::withMessages([
                    'subscription_period_id' => [
                        'An invoice already exists for this subscription period.',
                    ],
                ]);
            }

            /*
             * ---------------------------------------------------------
             * VALIDATE PERIOD DATES
             * ---------------------------------------------------------
             */
            if (!$period->starts_at || !$period->ends_at) {
                throw ValidationException::withMessages([
                    'subscription_period_id' => [
                        'Subscription period must have both start and end dates before generating an invoice.',
                    ],
                ]);
            }

            $periodStart = Carbon::parse(
                $period->starts_at
            );

            $periodEnd = Carbon::parse(
                $period->ends_at
            );

            if ($periodEnd->lte($periodStart)) {
                throw ValidationException::withMessages([
                    'subscription_period_id' => [
                        'Subscription period end date must be after the start date.',
                    ],
                ]);
            }

            /*
             * ---------------------------------------------------------
             * PRICING SNAPSHOT
             * ---------------------------------------------------------
             */
            $fullBasePrice = (float) $period->base_price;

            $includedUnits = (float) $period->included_units;

            $overageRate = (float) $period->overage_rate;

            /*
             * ---------------------------------------------------------
             * TOTAL USAGE
             * ---------------------------------------------------------
             */
            $usageUnits = (float) $period->usageEvents()
                ->sum('usage_units');

            /*
             * ---------------------------------------------------------
             * OVERAGE CALCULATION
             * ---------------------------------------------------------
             */
            $overageUnits = max(
                0,
                $usageUnits - $includedUnits
            );

            $overageAmount = round(
                $overageUnits * $overageRate,
                2
            );

            /*
             * ---------------------------------------------------------
             * FULL BILLING CYCLE
             * ---------------------------------------------------------
             *
             * Monthly example:
             *
             * Start:
             * 2026-10-01 00:00:00
             *
             * End:
             * 2026-10-31 23:59:59
             *
             * Yearly example:
             *
             * Start:
             * 2026-01-01 00:00:00
             *
             * End:
             * 2026-12-31 23:59:59
             */
            $fullCycleStart = $periodStart
                ->copy()
                ->startOfDay();

            if ($period->billing_cycle === 'yearly') {
                $fullCycleEnd = $fullCycleStart
                    ->copy()
                    ->addYear()
                    ->subSecond();
            } else {
                $fullCycleEnd = $fullCycleStart
                    ->copy()
                    ->addMonth()
                    ->subSecond();
            }

            /*
             * ---------------------------------------------------------
             * FULL CYCLE DETECTION
             * ---------------------------------------------------------
             *
             * IMPORTANT:
             * Compare DATE values instead of comparing microseconds.
             *
             * Database may store:
             * 2026-10-31 23:59:59
             *
             * Carbon endOfDay() may contain:
             * 2026-10-31 23:59:59.999999
             *
             * Therefore compare only date strings.
             */
            $isFullCycle =
                $periodStart
                    ->copy()
                    ->startOfDay()
                    ->toDateString()
                ===
                $fullCycleStart
                    ->copy()
                    ->startOfDay()
                    ->toDateString()
                &&
                $periodEnd
                    ->copy()
                    ->toDateString()
                ===
                $fullCycleEnd
                    ->copy()
                    ->toDateString();

            /*
             * ---------------------------------------------------------
             * FULL CYCLE DAYS
             * ---------------------------------------------------------
             */
            if ($period->billing_cycle === 'yearly') {
                $nextCycleStart = $fullCycleStart
                    ->copy()
                    ->addYear();
            } else {
                $nextCycleStart = $fullCycleStart
                    ->copy()
                    ->addMonth();
            }

            $fullCycleDays = $fullCycleStart->diffInDays(
                $nextCycleStart
            );

            if ($fullCycleDays <= 0) {
                throw ValidationException::withMessages([
                    'subscription_period_id' => [
                        'Invalid billing cycle duration.',
                    ],
                ]);
            }

            /*
             * ---------------------------------------------------------
             * ACTUAL PERIOD DAYS
             * ---------------------------------------------------------
             */
            if ($isFullCycle) {
                /*
                 * Full cycle:
                 *
                 * Monthly October:
                 * 31 days
                 *
                 * Yearly:
                 * 365 / 366 days
                 */
                $actualPeriodDays = $fullCycleDays;
            } else {
                /*
                 * Partial cycle.
                 */
                $actualPeriodDays = $periodStart
                    ->copy()
                    ->startOfDay()
                    ->diffInDays(
                        $periodEnd
                            ->copy()
                            ->startOfDay()
                    );

                /*
                 * Prevent zero-day proration.
                 */
                $actualPeriodDays = max(
                    1,
                    $actualPeriodDays
                );
            }

            /*
             * ---------------------------------------------------------
             * BASE PRICE / PRORATION
             * ---------------------------------------------------------
             */
            if ($isFullCycle) {
                /*
                 * Full billing cycle:
                 *
                 * Base = full plan price
                 * Proration = 0
                 */
                $baseAmount = round(
                    $fullBasePrice,
                    2
                );

                $prorationAmount = 0;
            } else {
                /*
                 * Partial billing cycle:
                 *
                 * Base = 0
                 * Proration = daily price × actual days
                 */
                $baseAmount = 0;

                $prorationAmount = round(
                    $fullBasePrice
                    * (
                        $actualPeriodDays
                        / $fullCycleDays
                    ),
                    2
                );
            }

            /*
             * ---------------------------------------------------------
             * SUBTOTAL
             * ---------------------------------------------------------
             */
            $subtotal = round(
                $baseAmount
                + $prorationAmount
                + $overageAmount,
                2
            );

            /*
             * ---------------------------------------------------------
             * TAX
             * ---------------------------------------------------------
             */
            $taxAmount = round(
                $subtotal
                * ($taxRate / 100),
                2
            );

            /*
             * ---------------------------------------------------------
             * TOTAL
             * ---------------------------------------------------------
             */
            $total = round(
                $subtotal + $taxAmount,
                2
            );

            /*
             * ---------------------------------------------------------
             * INVOICE NUMBER
             * ---------------------------------------------------------
             */
            $invoiceNumber = $this->generateInvoiceNumber(
                (int) $period->subscription->merchant_id
            );

            /*
             * ---------------------------------------------------------
             * CREATE INVOICE
             * ---------------------------------------------------------
             */
            $invoice = $this->repository->create([
                'merchant_id' => $period->subscription->merchant_id,

                'customer_id' => $period->subscription->customer_id,

                'subscription_id' => $period->subscription_id,

                'subscription_period_id' => $period->id,

                'invoice_number' => $invoiceNumber,

                'billing_period_start' => $periodStart,

                'billing_period_end' => $periodEnd,

                'base_amount' => $baseAmount,

                'overage_amount' => $overageAmount,

                'proration_amount' => $prorationAmount,

                'subtotal' => $subtotal,

                'tax_rate' => $taxRate,

                'tax_amount' => $taxAmount,

                'total' => $total,

                'currency' => $period->plan->currency,

                'status' => 'draft',

                'issued_at' => null,

                'due_at' => $dueAt
                    ? Carbon::parse($dueAt)
                    : null,

                'paid_at' => null,

                'metadata' => [
                    'usage_units' => $usageUnits,

                    'included_units' => $includedUnits,

                    'overage_units' => $overageUnits,

                    'overage_rate' => $overageRate,

                    'full_base_price' => $fullBasePrice,

                    'period_days' => $actualPeriodDays,

                    'full_cycle_days' => $fullCycleDays,

                    'is_full_cycle' => $isFullCycle,

                    'is_partial_period' => !$isFullCycle,

                    'prorated' => !$isFullCycle,
                ],
            ]);

            /*
             * ---------------------------------------------------------
             * BASE INVOICE ITEM
             * ---------------------------------------------------------
             */
            if ($baseAmount > 0) {
                $this->repository->createItem(
                    $invoice,
                    [
                        'item_type' => 'base',

                        'description' => sprintf(
                            '%s subscription',
                            $period->plan->name
                        ),

                        'quantity' => 1,

                        'unit_price' => $baseAmount,

                        'amount' => $baseAmount,

                        'metadata' => [
                            'type' => 'base',

                            'plan_id' => $period->plan_id,

                            'billing_cycle' => $period->billing_cycle,
                        ],
                    ]
                );
            }

            /*
             * ---------------------------------------------------------
             * PRORATION INVOICE ITEM
             * ---------------------------------------------------------
             */
            if ($prorationAmount > 0) {
                $this->repository->createItem(
                    $invoice,
                    [
                        'item_type' => 'proration',

                        'description' => sprintf(
                            '%s subscription - prorated billing',
                            $period->plan->name
                        ),

                        'quantity' => $actualPeriodDays,

                        'unit_price' => round(
                            $fullBasePrice
                            / $fullCycleDays,
                            6
                        ),

                        'amount' => $prorationAmount,

                        'metadata' => [
                            'type' => 'proration',

                            'plan_id' => $period->plan_id,

                            'full_base_price' => $fullBasePrice,

                            'period_days' => $actualPeriodDays,

                            'full_cycle_days' => $fullCycleDays,
                        ],
                    ]
                );
            }

            /*
             * ---------------------------------------------------------
             * OVERAGE INVOICE ITEM
             * ---------------------------------------------------------
             */
            if ($overageUnits > 0) {
                $this->repository->createItem(
                    $invoice,
                    [
                        'item_type' => 'overage',

                        'description' => sprintf(
                            'Overage usage - %s',
                            $period->plan->unit_name
                        ),

                        'quantity' => $overageUnits,

                        'unit_price' => $overageRate,

                        'amount' => $overageAmount,

                        'metadata' => [
                            'type' => 'overage',

                            'usage_units' => $usageUnits,

                            'included_units' => $includedUnits,

                            'overage_units' => $overageUnits,

                            'overage_rate' => $overageRate,
                        ],
                    ]
                );
            }

            /*
             * ---------------------------------------------------------
             * RETURN COMPLETE INVOICE
             * ---------------------------------------------------------
             */
            return $this->findById(
                $invoice->id
            );
        });
    }

    /**
     * Find invoice by ID.
     */
    public function findById(int $id): Invoice
    {
        $invoice = $this->repository->findById($id);

        if (!$invoice) {
            throw ValidationException::withMessages([
                'invoice_id' => [
                    'The selected invoice does not exist.',
                ],
            ]);
        }

        return $invoice;
    }

    /**
     * Get invoices by customer.
     */
    public function getByCustomer(
        int $customerId
    ): Collection {
        return $this->repository->getByCustomer(
            $customerId
        );
    }

    /**
     * Get invoices by merchant.
     */
    public function getByMerchant(
        int $merchantId
    ): Collection {
        return $this->repository->getByMerchant(
            $merchantId
        );
    }

    /**
     * Issue invoice.
     */
    public function issue(int $id): Invoice
    {
        $invoice = $this->findById($id);

        if ($invoice->status !== 'draft') {
            throw ValidationException::withMessages([
                'invoice_id' => [
                    'Only draft invoices can be issued.',
                ],
            ]);
        }

        $this->repository->update(
            $invoice,
            [
                'status' => 'issued',
                'issued_at' => now(),
            ]
        );

        return $this->findById($id);
    }

    /**
     * Mark invoice as paid.
     */
    public function markAsPaid(int $id): Invoice
    {
        $invoice = $this->findById($id);

        if ($invoice->status !== 'issued') {
            throw ValidationException::withMessages([
                'invoice_id' => [
                    'Only issued invoices can be marked as paid.',
                ],
            ]);
        }

        $this->repository->update(
            $invoice,
            [
                'status' => 'paid',
                'paid_at' => now(),
            ]
        );

        return $this->findById($id);
    }

    /**
     * Void invoice.
     */
    public function void(int $id): Invoice
    {
        $invoice = $this->findById($id);

        if ($invoice->status === 'paid') {
            throw ValidationException::withMessages([
                'invoice_id' => [
                    'Paid invoices cannot be voided.',
                ],
            ]);
        }

        if ($invoice->status === 'void') {
            throw ValidationException::withMessages([
                'invoice_id' => [
                    'Invoice is already void.',
                ],
            ]);
        }

        $this->repository->update(
            $invoice,
            [
                'status' => 'void',
            ]
        );

        return $this->findById($id);
    }

    /**
     * Generate merchant-specific invoice number.
     *
     * Example:
     * INV-1-000001
     * INV-1-000002
     */
    private function generateInvoiceNumber(
        int $merchantId
    ): string {
        $latestInvoice = Invoice::query()
            ->where(
                'merchant_id',
                $merchantId
            )
            ->latest('id')
            ->first();

        $nextNumber = $latestInvoice
            ? $latestInvoice->id + 1
            : 1;

        return sprintf(
            'INV-%d-%06d',
            $merchantId,
            $nextNumber
        );
    }
}