<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Support\Facades\Log;
use App\Models\SensorData;

class MqttSensorListener extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Listen for MQTT sensor data and store it';

    public function handle()
    {

        $server   = env('MQTT_HOST', '127.0.0.1');
        $port     = env('MQTT_PORT', 1883);
        $clientId = env('MQTT_CLIENT_ID', 'laravel-listener');
        $clean    = true;

        $connectionSettings = (new ConnectionSettings)
            ->setKeepAliveInterval(30)
            ->setConnectTimeout(10);

        $this->info("🚀 Starting MQTT sensor listener...");

        while (true) {
            try {
                $mqtt = new MqttClient($server, $port, $clientId);
                $mqtt->connect($connectionSettings, $clean);
                $this->info("✅ Connected to MQTT broker at {$server}:{$port}");

                // Subscribe to all sensors
                $mqtt->subscribe('/sensor/+/data', function (string $topic, string $message) {
                    $parts = explode('/', $topic);
                    $device = $parts[2] ?? 'unknown';

                    Log::info("📥 MQTT message from [$device]", [
                        'topic' => $topic,
                        'payload' => $message,
                    ]);

                    $data = json_decode($message, true);
                    if (is_array($data)) {
                        $data['device_id'] = $data['device_id'] ?? $device;
                        try {
                            SensorData::create($data);
                            Log::info("✅ Stored sensor data for $device");
                        } catch (\Throwable $e) {
                            Log::error("❌ DB error for $device", [
                                'error' => $e->getMessage(),
                                'data' => $data,
                            ]);
                        }
                    } else {
                        Log::warning("⚠️ Invalid JSON payload from $device", ['raw' => $message]);
                    }
                }, 0);

                $mqtt->loop(true); // blocks and auto-reconnects

            } catch (\Throwable $e) {
                Log::error('❌ MQTT loop crashed', ['error' => $e->getMessage()]);
                $this->error('MQTT error: ' . $e->getMessage());

                // Wait before retrying connection
                sleep(5);
            }
        }

        return 0;
    }
}