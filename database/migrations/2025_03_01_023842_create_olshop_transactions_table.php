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
        Schema::create('olshop_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->unique();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('promo_code_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->unsignedBigInteger('sub_total_amount');
            $table->unsignedBigInteger('grand_total_amount');
            $table->unsignedBigInteger('discount_amount');
            $table->string('province');
            $table->string('city_regency');
            $table->string('district');
            $table->string('vilage_subdistrict');
            $table->string('post_code');
            $table->string('address');
            $table->boolean('is_paid');
            $table->string('booking_trx');
            $table->string('proof');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olshop_transactions');
    }
};
