<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SensorDataController extends Controller
{
    public function store(Request $request)
    {
        if ($request->get('sid') !== env('TASMOTA_SID')) {
            abort(403, 'Invalid SID');
        }

        DB::table('tasmota_sensor_logs')->insert([
            'unit' => $request->get('unit'),
            'controller' => $request->get('controller'),
            'model' => $request->get('model'),
            'temp' => $request->get('temp'),
            'hum' => $request->get('hum'),
            'relay1_status' => $request->get('relay1'),
            'relay2_status' => $request->get('relay2'),
            'sid' => $request->get('sid'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }
    
}