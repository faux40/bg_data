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

}
