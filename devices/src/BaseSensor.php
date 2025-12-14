<?php

namespace Devices;

use React\EventLoop\TimerInterface;
use SensorData;

/**
 * Classe base para sensores que enviam dados periodicamente via UDP
 */
abstract class BaseSensor extends BaseDevice
{
    protected int $sensorUdpPort;
    protected float $sendInterval = 15.0;
    protected ?TimerInterface $sensorTimer = null;

    public function __construct(string $name, string $type, int $port, float $interval = 15.0)
    {
        parent::__construct($name, $type, $port, 'ACTIVE');
        $this->sendInterval = $interval;
        $this->sensorUdpPort = (int) ($_ENV['SENSOR_UDP_PORT'] ?? 6000);
    }

    protected function onStart(): void
    {
        $this->startPeriodicSending();
    }

    /**
     * Inicia envio periódico de dados
     */
    protected function startPeriodicSending(): void
    {
        // Envia imediatamente
        $this->sendSensorReading();

        // Configura timer para envios periódicos
        $this->sensorTimer = $this->loop->addPeriodicTimer($this->sendInterval, function () {
            $this->sendSensorReading();
        });
    }

    /**
     * Envia uma leitura para o Gateway via UDP
     */
    protected function sendSensorReading(): void
    {
        $reading = $this->generateReading();

        $sensorData = new SensorData();
        $sensorData->setDeviceName($this->name);
        $sensorData->setType($reading['type']);
        $sensorData->setValue($reading['value']);

        $binary = $sensorData->serializeToString();
        $sock = @stream_socket_client("udp://127.0.0.1:{$this->sensorUdpPort}");
        if ($sock) {
            fwrite($sock, $binary);
            fclose($sock);
        }
    }

    /**
     * Gera uma leitura do sensor
     */
    abstract protected function generateReading(): array;
}
