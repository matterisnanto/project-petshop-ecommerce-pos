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
        Schema::create('breedings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->foreignId('category_animals_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('breeds_id')->nullable()->constrained()->nullOnDelete();
            $table->string('photo', 255)->nullable();
            $table->text('description')->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('purchase_price', 8)->nullable();
            $table->decimal('selling_price', 8);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breedings');
    }
};
