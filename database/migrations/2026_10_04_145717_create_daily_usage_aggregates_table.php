<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_usage_aggregates', function (Blueprint $table) {
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

            $table->date('usage_date');

            $table->unsignedBigInteger('total_usage_units')->default(0);

            $table->unsignedBigInteger('event_count')->default(0);

            $table->timestamps();

            $table->unique(
                [
                    'subscription_id',
                    'subscription_period_id',
                    'usage_date',
                ],
                'daily_usage_subscription_period_date_unique'
            );

            $table->index(
                ['merchant_id', 'usage_date'],
                'daily_usage_merchant_date_index'
            );

            $table->index(
                ['customer_id', 'usage_date'],
                'daily_usage_customer_date_index'
            );

            $table->index(
                ['subscription_id', 'usage_date'],
                'daily_usage_subscription_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_usage_aggregates');
    }
};