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
        Schema::create('pos_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20)->nullable();
            $table->string('phone', 13)->nullable();
            $table->string('email', 20)->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->decimal('total_price', 8);
            $table->text('note')->nullable();
            $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('paid_amount', 8)->nullable();
            $table->decimal('change_amount', 8)->nullable();
            $table->string('trx_id', 15)->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_transactions');
    }
};
