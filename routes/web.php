<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\SensorDataController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Route::post('/sensor', [SensorDataController::class, 'store']);
Route::get('/check-sid', function () {
    return env('TASMOTA_SID');
});

// Route::get('/sensor-data', [SensorDataController::class, 'index']);
Route::get('/sensor-data', [SensorDataController::class, 'index'])->name('sensor-data.index');




require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
