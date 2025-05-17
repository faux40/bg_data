<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    protected $fillable = [
        'device_id',
        'sid',
        'temperature',
        'temp_unit',
        'humidity',
        'pm1_0_std',
        'pm2_5_std',
        'pm10_0_std',
        'pm1_0_env',
        'pm2_5_env',
        'pm10_0_env',
        'particles_0_3um',
        'particles_0_5um',
        'particles_1_0um',
        'particles_2_5um',
        'particles_5_0um',
        'particles_10_0um',
    ];


    // ✅ Virtual accessors
    public function getTemperatureFAttribute(): ?float
    {
        return $this->temperature !== null
            ? round($this->temperature * 1.8 + 32, 2)
            : null;
    }

    public function getHeatIndexFAttribute(): ?float
    {
        if ($this->temperature === null || $this->humidity === null) {
            return null;
        }

        $t = $this->temperature * 1.8 + 32; // tempF
        $h = $this->humidity;

        // Use formula only for applicable range
        if ($t < 80 || $h < 40) {
            return $t;
        }

        $hi = -42.379
            + 2.04901523 * $t
            + 10.14333127 * $h
            - 0.22475541 * $t * $h
            - 0.00683783 * $t * $t
            - 0.05481717 * $h * $h
            + 0.00122874 * $t * $t * $h
            + 0.00085282 * $t * $h * $h
            - 0.00000199 * $t * $t * $h * $h;

        return round($hi, 2);
    }


}