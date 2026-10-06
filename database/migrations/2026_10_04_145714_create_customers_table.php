<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Merchant relationship
            $table->foreignId('merchant_id')
                ->constrained('merchants')
                ->cascadeOnDelete();

            // Customer identity
            $table->string('code', 50);
            $table->string('name');

            // Contact details
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();

            // Customer status
            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

            // Additional flexible data
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Customer code must be unique within a merchant
            $table->unique(
                ['merchant_id', 'code'],
                'customers_merchant_id_code_unique'
            );

            // Email can be queried frequently
            $table->index('email');

            // Merchant + status filtering
            $table->index(
                ['merchant_id', 'status'],
                'customers_merchant_id_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
