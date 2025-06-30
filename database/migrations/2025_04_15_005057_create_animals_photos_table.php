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
        Schema::create('animals_photos', function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD:database/migrations/2025_04_15_005057_create_animals_photos_table.php
            $table->string('photo', 255);
            $table->foreignId('animals_id')->constrained()->cascadeOnDelete();
=======
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('barcode')->nullable();
            $table->string('thumbnail');
            $table->longText('about')->nullable();
            $table->unsignedBigInteger('price');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular');
            
            $table->integer('stock')->default();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete(); 
            $table->foreignId('brand_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->cascadeOnDelete();
>>>>>>> 964922ec0c3bc7ab9aea8f6f681f165132b3bbc3:database/migrations/2025_02_28_222426_create_products_table.php
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals_photos');
    }
};