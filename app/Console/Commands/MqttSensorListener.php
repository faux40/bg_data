<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Models\SensorData;

class MqttSensorListener extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Listen to MQTT sensor data and store it';

    public function handle()
    {
        $host = env('MQTT_HOST', '127.0.0.1');
        $port = env('MQTT_PORT', 1883);
        $clientId = 'laravel-mqtt-listener';

        $connectionSettings = (new ConnectionSettings)->setUsername(null)->setPassword(null);
        $mqtt = new MqttClient($host, $port, $clientId);
        $mqtt->connect($connectionSettings, true);

        $mqtt->subscribe('/sensor/+/data', function (string $topic, string $message) {
            $data = json_decode($message, true);

            if ($data && isset($data['temperature'], $data['humidity'])) {
                SensorData::create([
                    'device_id' => $data['device_id'] ?? null,
                    'sid' => $data['sid'] ?? null,
                    'temperature' => $data['temperature'],
                    'humidity' => $data['humidity'],
                    'temp_unit' => 'C',
                ]);
                echo "✅ Saved data: {$data['temperature']}°C, {$data['humidity']}%\n";
            } else {
                echo "⚠️ Invalid message: $message\n";
            }
        }, 0);

        $mqtt->loop(true);
    }
}