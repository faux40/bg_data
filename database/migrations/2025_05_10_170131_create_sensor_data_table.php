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
            $table->string('temp_unit')->nullable();
            $table->decimal('hum', 5, 2)->nullable();
            $table->decimal('dewpoint', 5, 2)->nullable();
            $table->string('relay1')->nullable();
            $table->string('relay1')->nullable();
            $table->string('sid')->nullable();
            $table->timestampsTz();
        });
    }

    // Rule2 on Rules#Timer=1 do backlog WebSend [data.barrittgroup.com] /api/sensor?unit=thr316d-office&temp=%var1%&hum=%var2%&dewpoint=%var3%&relay1=%power1%&relay2=%power2%&sid=675bc5a5e800d0c39b9bfc47bc599016; RuleTimer1 60 endon
    // 15:55:32.921 RUL: RULES#TIMER=1 performs 'backlog WebSend [data.barrittgroup.com] 
            // /api/sensor?unit=thr316d-office
            // &temp=24.6
            // &hum=36.2
            // &dewpoint=8.6
            // &relay1=0
            // &relay2=1
            // &sid=675bc5a5e800d0c39b9bfc47bc599016; RuleTimer1 60'

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_data');
    }
};
