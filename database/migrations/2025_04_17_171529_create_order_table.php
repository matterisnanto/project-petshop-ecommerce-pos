<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_transaction_id')->nullable()->constrained('pos_transactions')->cascadeOnDelete();
            $table->foreignId('olshop_transaction_id')->nullable()->constrained('olshop_transactions')->cascadeOnDelete();
            $table->string('type')->nullable();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('animals_id')->nullable()->constrained('animals')->nullOnDelete();
            $table->foreignId('grooming_id')->nullable()->constrained('groomings')->nullOnDelete();
            $table->foreignId('hotel_id')->nullable()->constrained('hotels')->nullOnDelete();
            $table->integer('quantity');
            $table->integer('unit_price');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
