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
            $table->string('name', 100);
            $table->string('phone', 20);
            $table->string('email', 100);
            $table->decimal('sub_total_amount', 12, 2);
            $table->foreignId('promo_code_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('grand_total_amount', 12, 2);
            $table->string('province', 100);
            $table->string('city_regency', 100);
            $table->string('post_code', 10);
            $table->text('complete_address');
            $table->boolean('is_paid')->default(false);
            $table->string('trx_id', 50)->unique();
            $table->string('package_resi_number', 50)->default('Being Processed');
            $table->string('courier', 50);
            $table->string('shipping_service', 255);
            $table->decimal('weight_total', 8, 2);
            $table->decimal('shipping_cost', 12, 2);
            $table->string('estimated_delivery', 100);
            $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete();
            $table->string('proof', 255);
            $table->timestamps();
            $table->softDeletes();
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
