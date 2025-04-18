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
        Schema::create('grooming_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('price')->nullable();
            $table->longText('description')->nullable();
            $table->foreignId('grooming_id')->constrained('groomings')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grooming_packages');
    }
};
