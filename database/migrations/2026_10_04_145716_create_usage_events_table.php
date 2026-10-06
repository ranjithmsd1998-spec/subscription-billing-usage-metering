<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_events', function (Blueprint $table) {
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
                ->cascadeOnDelete();

            $table->string('idempotency_key');

            $table->unsignedBigInteger('usage_units');

            $table->string('unit_name')->default('units');

            $table->dateTime('occurred_at');

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique(
                ['merchant_id', 'idempotency_key'],
                'usage_events_merchant_id_idempotency_unique'
            );

            $table->index(
                ['subscription_id', 'occurred_at'],
                'usage_events_subscription_occurred_index'
            );

            $table->index(
                ['customer_id', 'occurred_at'],
                'usage_events_customer_occurred_index'
            );

            $table->index(
                ['subscription_period_id', 'occurred_at'],
                'usage_events_period_occurred_index'
            );

            $table->index(
                ['merchant_id', 'occurred_at'],
                'usage_events_merchant_occurred_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_events');
    }
};