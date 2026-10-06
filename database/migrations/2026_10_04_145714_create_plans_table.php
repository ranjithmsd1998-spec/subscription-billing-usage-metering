<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('merchant_id')
                ->constrained('merchants')
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('code');

            $table->text('description')
                ->nullable();

            $table->char('currency', 3)
                ->default('USD');

            $table->decimal('base_price', 12, 2);

            $table->enum('billing_cycle', [
                'monthly',
                'yearly',
            ])->default('monthly');

            $table->unsignedBigInteger('included_units')
                ->default(0);

            $table->decimal('overage_rate', 12, 6)
                ->default(0);

            $table->string('unit_name')
                ->default('units');

            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

            $table->timestamps();

            $table->unique(
                ['merchant_id', 'code'],
                'plans_merchant_id_code_unique'
            );

            $table->index(
                ['merchant_id', 'status'],
                'plans_merchant_id_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};