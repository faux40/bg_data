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
        Schema::create('sensor_data', function (Blueprint $table) {
            $table->id();
            $table->string('unit')->nullable();
            $table->string('controller')->nullable();
            $table->string('model')->nullable();
            $table->decimal('temp', 5, 2)->nullable();
            $table->decimal('hum', 5, 2)->nullable();
            $table->string('relay1_status')->nullable();
            $table->string('relay2_status')->nullable();
            $table->string('sid')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_data');
    }
};
