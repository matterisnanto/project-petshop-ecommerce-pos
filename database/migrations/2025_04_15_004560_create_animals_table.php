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
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $$table->string('slug')->unique();
            $table->foreignId('category_animals_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('breeds_id')->nullable()->constrained()->nullOnDelete();
            $table->string('age');
            $table->string('weight');
            $table->enum('gender', ['male', 'female', 'unknown'])->default('unknown');
            $table->string('health_status');
            $table->boolean('vaccination_status')->default(false);
            $table->string('thumbnail');
            $table->longText('description')->nullable();
            $table->integer('stock')->default();
            $table->unsignedBigInteger('purchase_price');
            $table->unsignedBigInteger('selling_price');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
