<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use Illuminate\Support\Facades\Http;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Support\Facades\Log;

class MqttSensorListener extends Command
{

    protected $signature = 'mqtt:listen';
    protected $description = 'Subscribe to MQTT topic and log raw messages for debug';

    public function handle()
    {
        $server   = env('MQTT_HOST', 'data.barrittgroup.com');
        $port     = env('MQTT_PORT', 1883);
        $clientId = env('MQTT_CLIENT_ID', 'raw-debugger');

        $this->info("🔌 Connecting to MQTT at $server:$port");

        $mqtt = new MqttClient($server, $port, $clientId);

        try {
            $mqtt->connect(new ConnectionSettings(), true);
            $this->info("✅ Connected to MQTT broker");
        } catch (\Throwable $e) {
            $this->error("❌ Failed to connect: " . $e->getMessage());
            Log::error('❌ MQTT connection failed', ['error' => $e->getMessage()]);
            return 1;
        }

        // $mqtt->subscribe('/sensor/#', function (string $topic, string $message) {
        //     Log::info('📥 RAW MQTT', ['topic' => $topic, 'message' => $message]);
        // }, 0);

// $mqtt->subscribe('/sensor/#', function (string $topic, string $message) {
//     Log::info('📥 MQTT received', ['topic' => $topic, 'payload' => $message]);

//     try {
//         $response = Http::post('https://data.barrittgroup.com/api/sensor', json_decode($message, true));
//         Log::info('✅ Forwarded to API', ['status' => $response->status(), 'body' => $response->body()]);
//     } catch (\Throwable $e) {
//         Log::error('❌ Failed to POST to /api/sensor', ['error' => $e->getMessage()]);
//     }
// });
// inside: $mqtt->subscribe(...)
$mqtt->subscribe('/sensor/#', function (string $topic, string $message) {
    Log::info('📥 RAW MQTT XXX', ['topic' => $topic, 'message' => $message]);

    try {
        $payload = json_decode($message, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($payload)) {
            Log::warning("⚠️ Invalid JSON payload: " . $message);
            return;
        }

        // ✅ Forward to internal API (adjust if needed)
        $response = Http::post('https://data.barrittgroup.com/api/sensor', $payload);

        Log::info('📤 Forwarded to API', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);
    } catch (\Throwable $e) {
        Log::error('❌ Failed to forward to sensor API', ['error' => $e->getMessage()]);
    }
});


        $this->info("📡 Listening for messages on /sensor/# ...");

        try {
            $mqtt->loop(true);
        } catch (\Throwable $e) {
            Log::critical('🔥 MQTT loop crashed', ['error' => $e->getMessage()]);
            $this->error("🔥 Loop failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}