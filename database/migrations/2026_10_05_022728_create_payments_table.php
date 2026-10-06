<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('merchant_id')
                ->constrained('merchants')
                ->cascadeOnDelete();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->cascadeOnDelete();

            $table->string('payment_reference');

            $table->decimal('amount', 12, 2);

            $table->char('currency', 3);

            $table->enum('payment_method', [
                'cash',
                'bank_transfer',
                'card',
                'upi',
                'other',
            ]);

            $table->enum('status', [
                'pending',
                'completed',
                'failed',
                'refunded',
            ])->default('pending');

            $table->dateTime('paid_at')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique(
                ['merchant_id', 'payment_reference'],
                'payments_merchant_reference_unique'
            );

            $table->index(
                ['invoice_id', 'status'],
                'payments_invoice_status_index'
            );

            $table->index(
                ['customer_id', 'status'],
                'payments_customer_status_index'
            );

            $table->index(
                ['merchant_id', 'status'],
                'payments_merchant_status_index'
            );

            $table->index(
                ['paid_at'],
                'payments_paid_at_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};