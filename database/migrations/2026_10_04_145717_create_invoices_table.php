<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('merchant_id')
                ->constrained('merchants')
                ->cascadeOnDelete();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->foreignId('subscription_id')
                ->constrained('subscriptions')
                ->cascadeOnDelete();

            $table->foreignId('subscription_period_id')
                ->constrained('subscription_periods')
                ->restrictOnDelete();

            /*
             * Invoice identification
             */
            $table->string('invoice_number');

            /*
             * Billing period
             */
            $table->dateTime('billing_period_start');

            $table->dateTime('billing_period_end');

            /*
             * Invoice calculation
             */
            $table->decimal('base_amount', 12, 2)->default(0);

            $table->decimal('overage_amount', 12, 2)->default(0);

            $table->decimal('proration_amount', 12, 2)->default(0);

            $table->decimal('subtotal', 12, 2)->default(0);

            /*
             * Tax snapshot and calculated tax
             */
            $table->decimal('tax_rate', 5, 2)->default(0);

            $table->decimal('tax_amount', 12, 2)->default(0);

            /*
             * Final payable amount
             */
            $table->decimal('total', 12, 2)->default(0);

            /*
             * Currency snapshot
             */
            $table->char('currency', 3)->default('USD');

            /*
             * Invoice status
             */
            $table->enum('status', [
                'draft',
                'issued',
                'paid',
                'void',
            ])->default('draft');

            /*
             * Invoice lifecycle dates
             */
            $table->dateTime('issued_at')->nullable();

            $table->dateTime('due_at')->nullable();

            $table->dateTime('paid_at')->nullable();

            /*
             * Additional invoice information
             */
            $table->json('metadata')->nullable();

            $table->timestamps();

            /*
             * Invoice number must be unique per merchant.
             */
            $table->unique(
                ['merchant_id', 'invoice_number'],
                'invoices_merchant_id_invoice_number_unique'
            );

            /*
             * Customer invoice lookup.
             */
            $table->index(
                ['customer_id', 'status'],
                'invoices_customer_status_index'
            );

            /*
             * Subscription billing-period lookup.
             */
            $table->index(
                [
                    'subscription_id',
                    'billing_period_start',
                    'billing_period_end',
                ],
                'invoices_subscription_billing_period_index'
            );

            /*
             * Merchant invoice lookup.
             */
            $table->index(
                ['merchant_id', 'status'],
                'invoices_merchant_status_index'
            );

            /*
             * Due-date lookup for payment/overdue processing.
             */
            $table->index(
                ['status', 'due_at'],
                'invoices_status_due_at_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};