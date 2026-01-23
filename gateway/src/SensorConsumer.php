<?php

namespace Gateway;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use React\EventLoop\LoopInterface;
use SensorData;

class SensorConsumer
{
    public function start(DeviceRegistry $registry, LoopInterface $loop): void
    {
        $host = $_ENV['RABBITMQ_HOST'] ?? 'rabbitmq';
        $user = $_ENV['RABBITMQ_DEFAULT_USER'] ?? 'guest'; // Se usar admin no docker-compose, mude aqui ou no .env
        $pass = $_ENV['RABBITMQ_DEFAULT_PASS'] ?? 'guest';

        try {
            $connection = new AMQPStreamConnection($host, 5672, $user, $pass);
            $channel = $connection->channel();

            $exchange = 'sensors.topic';
            $queue = 'gateway.sensors';

            $channel->exchange_declare($exchange, 'topic', false, true, false);
            $channel->queue_declare($queue, false, true, false, false);
            $channel->queue_bind($queue, $exchange, 'sensor.#');

            echo "[RABBIT] Conectado e aguardando mensagens...\n";

            $channel->basic_consume(
                $queue,
                '',
                false,
                true,
                false,
                false,
                function ($msg) use ($registry) {
                    try {
                        $data = new SensorData();
                        $data->mergeFromString($msg->body);
                        
                        $registry->updateSensorData(
                            $data->getDeviceName(),
                            $data->getType(),
                            $data->getValue()
                        );
                    } catch (\Throwable $e) {
                        echo "[RABBIT-ERR] Decode falhou: {$e->getMessage()}\n";
                    }
                }
            );
            
            $io = $connection->getIO();
            
            $reflection = new \ReflectionClass($io);
            $prop = $reflection->getProperty('sock');
            $prop->setAccessible(true);
            $socket = $prop->getValue($io);

            $loop->addReadStream($socket, function() use ($channel) {
                try {
                    $channel->wait(null, true);
                } catch (\Throwable $e) {
                    echo "[RABBIT-CONN] Erro de leitura: " . $e->getMessage() . "\n";
                }
            });

        } catch (\Exception $e) {
            echo "[RABBIT-FATAL] Não foi possível conectar: " . $e->getMessage() . "\n";
        }
    }
}