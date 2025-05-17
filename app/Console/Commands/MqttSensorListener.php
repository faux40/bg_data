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
        $server   = '127.0.0.1';
        $port     = 1883;
        $clientId = 'laravel-listener';
        $clean    = true;

        $connectionSettings = (new ConnectionSettings)
            ->setKeepAliveInterval(30)
            ->setConnectTimeout(10);

        $mqtt = new MqttClient($server, $port, $clientId);

        try {
            $mqtt->connect($connectionSettings, $clean);
            $this->info("✅ Connected to MQTT broker at {$server}:{$port}");
        } catch (\Throwable $e) {
            Log::error('❌ Failed to connect to MQTT broker', ['error' => $e->getMessage()]);
            $this->error('Could not connect to MQTT broker.');
            return 1;
        }

        // ✅ Subscribe to wildcard topic once
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

        try {
            $this->info('🔁 Starting MQTT loop...');
            $mqtt->loop(true); // auto-reconnect enabled
        } catch (\Throwable $e) {
            Log::critical('❌ MQTT loop crashed', ['error' => $e->getMessage()]);
            $this->error('MQTT loop failed: ' . $e->getMessage());
        }

        return 0;
    }
}