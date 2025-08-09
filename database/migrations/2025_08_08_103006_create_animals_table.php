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
            $table->string('name', 50);
            $table->string('slug', 55)->unique();
            $table->string('barcode', 20)->nullable()->unique();
            $table->foreignId('category_animals_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('breeds_id')->nullable()->constrained('breeds')->nullOnDelete();
            $table->string('age', 2);
            $table->decimal('weight', 8);
            $table->enum('gender', ['male', 'female', 'unknown'])->default('unknown');
            $table->string('health_status', 10);
            $table->boolean('vaccination_status')->default(false);
            $table->string('thumbnail', 255);
            $table->longText('description')->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('purchase_price', 8);
            $table->decimal('selling_price', 8);
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
