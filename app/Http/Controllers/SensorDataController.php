<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        // Log::info('Received Tasmota data', $request->all());

    // ✅ Log the entire query string or selected fields
    // Log::info('Received sensor data', $request->all());
        // if ($request->get('sid') !== env('TASMOTA_SID')) {
        //     return response()->json(['error' => 'Invalid SID'], 403);
        // }
        // if ($request->get('sid') !== '675bc5a5e800d0c39b9bfc47bc599016') {
        //     return response()->json(['error' => 'Invalid SID'], 403);
        // }
    
        DB::table('sensor_data')->insert([
            'unit' => $request->get('unit'),
            'controller' => $request->get('controller'),
            'model' => $request->get('model'),
            'temp' => $request->get('temp'),
            'hum' => $request->get('hum'),
            'dewpoint' => $request->get('dewpoint'),
            'relay1' => $request->get('relay1'),
            'relay2' => $request->get('relay2'),
            'sid' => $request->get('sid'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
    //     return response('OK', 200)
    //    ->header('Content-Type', 'text/plain');


        return response()->json(['ok' => true]);
    }
}