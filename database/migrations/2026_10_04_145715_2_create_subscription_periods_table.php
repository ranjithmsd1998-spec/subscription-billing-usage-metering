<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_periods', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscription_id')
                ->constrained('subscriptions')
                ->cascadeOnDelete();

            $table->foreignId('plan_id')
                ->constrained('plans')
                ->restrictOnDelete();

            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();

            // Pricing snapshot at the time this period was created.
            $table->decimal('base_price', 12, 2);
            $table->unsignedBigInteger('included_units')->default(0);
            $table->decimal('overage_rate', 12, 6)->default(0);

            $table->enum('billing_cycle', [
                'monthly',
                'yearly',
            ])->default('monthly');

            $table->timestamps();

            // Efficient lookup of periods for a subscription and date range.
            $table->index(
                ['subscription_id', 'starts_at', 'ends_at'],
                'subscription_periods_subscription_dates_index'
            );

            // Useful for billing and plan-history queries.
            $table->index(
                ['plan_id', 'starts_at'],
                'subscription_periods_plan_starts_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_periods');
    }
};