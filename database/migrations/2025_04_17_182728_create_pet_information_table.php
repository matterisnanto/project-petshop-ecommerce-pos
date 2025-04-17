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
            $table->foreignId('order_id')->constrained('order')->cascadeOnDelete();
            $table->string('name');
            $table->integer('age');
            $table->string('photo');
            $table->string('description');
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('days');
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
