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
        Schema::create('transaction_returns', function (Blueprint $table) {
            $table->id();
            $table->date('return_date');
            $table->string('return_number', 10);
            $table->string('type', 10)->nullable();
            $table->foreignId('pos_transaction_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('olshop_transaction_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('refund_amount', 8)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'processed', 'refunded'])->default('pending');
            $table->date('return_approved_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_returns');
    }
};
