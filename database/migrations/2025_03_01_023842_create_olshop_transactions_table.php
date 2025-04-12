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
            $table->string('email');
            $table->unsignedBigInteger('sub_total_amount');
            $table->foreignId('promo_code_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('discount_amount');
            $table->unsignedBigInteger('grand_total_amount');
            $table->string('province');
            $table->string('city_regency');
            $table->string('post_code');
            $table->string('complete_address');
            $table->boolean('is_paid')->default(false);
            $table->string('trx_id');
            $table->string('package_resi_number')->default('Being Processed');
            $table->string('courier');
            $table->string('shipping_service');
            $table->bigInteger('weight_total');
            $table->bigInteger('shipping_cost');
            $table->string('estimated_delivery');
            $table->foreignId('payment_method_id')
                ->nullable()
                ->constrained('payment_methods')
                ->nullOnDelete();
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
