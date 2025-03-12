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
            $table->unsignedBigInteger('sub_total_amount');
            $table->foreignId('promo_code_id')->nullable()->constrained()->nullOnDelete();
            // $table->integer('quantity');
            $table->unsignedBigInteger('discount_amount');
            $table->unsignedBigInteger('grand_total_amount');
            $table->string('province');
            $table->string('city_regency');
            $table->string('district');
            $table->string('vilage_subdistrict');
            $table->string('post_code');
            $table->string('address');
            $table->boolean('is_paid');
            $table->string('trx_id');
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
