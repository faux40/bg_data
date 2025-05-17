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
            
            $table->string('device_id')->nullable();
            $table->string('sid')->nullable();

            $table->decimal('temperature', 5, 2)->nullable();
            $table->string('temp_unit')->nullable();
            $table->decimal('humidity', 5, 2)->nullable();

            $table->float('pm1_0_std')->nullable();
            $table->float('pm2_5_std')->nullable();
            $table->float('pm10_0_std')->nullable();
            $table->float('pm1_0_env')->nullable();
            $table->float('pm2_5_env')->nullable();
            $table->float('pm10_0_env')->nullable();
            $table->integer('particles_0_3um')->nullable();
            $table->integer('particles_0_5um')->nullable();
            $table->integer('particles_1_0um')->nullable();
            $table->integer('particles_2_5um')->nullable();
            $table->integer('particles_5_0um')->nullable();
            $table->integer('particles_10_0um')->nullable();

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
