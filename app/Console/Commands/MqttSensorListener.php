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
            $this->info('✅ Connected to MQTT broker at ' . $server . ':' . $port);
        } catch (\Throwable $e) {
            Log::error('❌ Failed to connect to MQTT broker', ['error' => $e->getMessage()]);
            $this->error('Could not connect to MQTT broker.');
            return 1;
        }

        // ✅ Subscribe to topic (exact)
        $mqtt->subscribe('/sensor/nano-office/data', function (string $topic, string $message) {
            Log::info('📥 MQTT received', [
                'topic' => $topic,
                'payload' => $message,
            ]);

            $data = json_decode($message, true);

            try {
                SensorData::create($data);
                Log::info('✅ SensorData saved', ['sid' => $data['sid'] ?? 'n/a']);
            } catch (\Throwable $e) {
                Log::error('❌ Failed to store SensorData', ['error' => $e->getMessage(), 'payload' => $data]);
            }
        }, 0);

        // 🔄 Loop forever with error handling
        while (true) {
            try {
                $mqtt->loopOnce(true);
            } catch (\Throwable $e) {
                Log::error('❌ MQTT loop error', ['error' => $e->getMessage()]);
                $this->warn('⚠️ MQTT loop error: ' . $e->getMessage());
                sleep(2);

                try {
                    $mqtt->disconnect();
                } catch (\Throwable $inner) {
                    Log::warning('⚠️ MQTT disconnect failed', ['error' => $inner->getMessage()]);
                }

                try {
                    $mqtt->connect($connectionSettings, $clean);
                    $this->info('🔁 Reconnected to MQTT broker');

                    // Re-subscribe after reconnect
                    $mqtt->subscribe('/sensor/nano-office/data', function (string $topic, string $message) {
                        Log::info('📥 MQTT (re)received', [
                            'topic' => $topic,
                            'payload' => $message,
                        ]);

                        $data = json_decode($message, true);
                        SensorData::create($data);
                    }, 0);
                } catch (\Throwable $connectFail) {
                    Log::error('❌ MQTT reconnect failed', ['error' => $connectFail->getMessage()]);
                    sleep(10);
                }
            }

            usleep(500000); // half-second delay
        }
    }
}