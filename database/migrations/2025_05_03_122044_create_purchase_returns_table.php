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
        Schema::create('purchase_return', function (Blueprint $table) {
            $table->id();
            $table->date('return_date');
            $table->string('return_number');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('purchases_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('refund_amount', 12, 2)->nullable();
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
        Schema::dropIfExists('purchase_returns');
    }
};
