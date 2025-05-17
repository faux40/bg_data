<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

    use App\Models\SensorData;


class SensorDataController extends Controller
{
    public function index()
    {
        // Replace 'sensor_data_table' with your actual table name
        // $data = DB::table('sensor_data')->orderByDesc('created_at')->limit(100)->get();
        $data = DB::table('sensor_data')->orderBy('created_at', 'desc')->limit(100)->get();

        return view('sensor-data.index', compact('data'));
    }



public function store(Request $request)
{
    $data = $request->validate([
        'device_id' => 'nullable|string|max:255',
        'sid' => 'nullable|string|max:255',
        'temperature' => 'nullable|numeric',
        'temp_unit' => 'nullable|string|max:5',
        'humidity' => 'nullable|numeric',

        'pm1_0_std' => 'nullable|numeric',
        'pm2_5_std' => 'nullable|numeric',
        'pm10_0_std' => 'nullable|numeric',
        'pm1_0_env' => 'nullable|numeric',
        'pm2_5_env' => 'nullable|numeric',
        'pm10_0_env' => 'nullable|numeric',

        'particles_0_3um' => 'nullable|integer',
        'particles_0_5um' => 'nullable|integer',
        'particles_1_0um' => 'nullable|integer',
        'particles_2_5um' => 'nullable|integer',
        'particles_5_0um' => 'nullable|integer',
        'particles_10_0um' => 'nullable|integer',
    ]);

    $entry = SensorData::create($data);

    return response()->json([
        'status' => 'ok',
        'id' => $entry->id,
        'created_at' => $entry->created_at,
    ]);
}


}