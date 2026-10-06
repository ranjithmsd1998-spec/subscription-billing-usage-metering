<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('merchant_id')
                ->constrained('merchants')
                ->cascadeOnDelete();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->foreignId('plan_id')
                ->constrained('plans')
                ->restrictOnDelete();

            $table->enum('status', [
                'active',
                'cancelled',
                'expired',
            ])->default('active');

            $table->dateTime('started_at');

            $table->dateTime('current_period_start');
            $table->dateTime('current_period_end');

            $table->dateTime('cancelled_at')->nullable();

            $table->timestamps();

            // Fast lookup of subscriptions by merchant and status.
            $table->index(
                ['merchant_id', 'status'],
                'subscriptions_merchant_id_status_index'
            );

            // Fast lookup of customer subscriptions.
            $table->index(
                ['customer_id', 'status'],
                'subscriptions_customer_id_status_index'
            );

            // Useful for finding subscriptions by plan.
            $table->index(
                ['plan_id', 'status'],
                'subscriptions_plan_id_status_index'
            );

            // Useful for billing-period queries.
            $table->index(
                ['current_period_start', 'current_period_end'],
                'subscriptions_current_period_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};