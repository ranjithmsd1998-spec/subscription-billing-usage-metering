<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->cascadeOnDelete();

            $table->enum('item_type', [
                'base',
                'overage',
                'proration',
            ]);

            $table->string('description');

            $table->decimal('quantity', 14, 4)->default(1);

            $table->decimal('unit_price', 12, 6)->default(0);

            $table->decimal('amount', 12, 2)->default(0);

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(
                ['invoice_id', 'item_type'],
                'invoice_items_invoice_type_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};