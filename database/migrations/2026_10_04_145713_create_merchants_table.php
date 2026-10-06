<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();

            // Merchant identity
            $table->string('name');
            $table->string('code')->unique();

            // Contact details
            $table->string('email')->nullable()->unique();
            $table->string('phone', 20)->nullable();

            // Merchant configuration
            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

            $table->string('timezone', 100)->default('UTC');
            $table->char('currency', 3)->default('USD');

            // Additional flexible data
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Query optimization
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};