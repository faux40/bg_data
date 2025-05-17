<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Support\Facades\Log;

class MqttRawLogger extends Command
{
    protected $signature = 'mqtt:lograw';
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

        $mqtt->subscribe('/sensor/#', function (string $topic, string $message) {
            Log::info('📥 RAW MQTT', ['topic' => $topic, 'message' => $message]);
        }, 0);

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