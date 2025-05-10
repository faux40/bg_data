<?php

use App\Http\Controllers\SensorDataController;
use Illuminate\Support\Facades\Route;

// Route::post('/sensor', [SensorDataController::class, 'store']);
Route::get('/sensor', [SensorDataController::class, 'store']);