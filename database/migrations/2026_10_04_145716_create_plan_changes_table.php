<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_changes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscription_id')
                ->constrained('subscriptions')
                ->cascadeOnDelete();

            $table->foreignId('from_plan_id')
                ->nullable()
                ->constrained('plans')
                ->restrictOnDelete();

            $table->foreignId('to_plan_id')
                ->constrained('plans')
                ->restrictOnDelete();

            $table->dateTime('effective_at');

            $table->enum('change_type', [
                'upgrade',
                'downgrade',
            ]);

            $table->string('reason')
                ->nullable();

            $table->json('metadata')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['subscription_id', 'effective_at'],
                'plan_changes_subscription_effective_index'
            );

            $table->index(
                ['from_plan_id', 'effective_at'],
                'plan_changes_from_plan_effective_index'
            );

            $table->index(
                ['to_plan_id', 'effective_at'],
                'plan_changes_to_plan_effective_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_changes');
    }
};