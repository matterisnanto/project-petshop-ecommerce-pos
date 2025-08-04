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
        Schema::create('pet_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detail_order_id')->constrained('detail_order')->cascadeOnDelete();
            $table->string('name', 50);
            $table->foreignId('category_animals_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('age', 2);
            $table->string('photo', 255);
            $table->text('description')->nullable();
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
            $table->integer('days', 3)->nullable();
            $table->boolean('on_petshop')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pet_information');
    }
};
