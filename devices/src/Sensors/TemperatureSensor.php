<?php

namespace Devices\Sensors;

use Devices\BaseSensor;
use Command;
use CommandResponse;

/**
 * Sensor de Temperatura
 * 
 * Envia leituras periódicas de temperatura ao Gateway via UDP
 * 
 * Comandos: SET_INTERVAL, GET_READING, GET_STATUS
 */
class TemperatureSensor extends BaseSensor
{
    private string $unit = 'CELSIUS';
    private float $currentTemp;
    private float $baseTemp;

    public function __construct(string $name, int $port, float $interval = 15.0)
    {
        parent::__construct($name, 'TEMPERATURE_SENSOR', $port, $interval);
        
        $this->baseTemp = mt_rand(150, 300) / 10.0;
        $this->currentTemp = $this->baseTemp;
    }

    protected function generateReading(): array
    {
        $change = (mt_rand(-10, 10) / 10.0) * 0.5;
        $this->currentTemp = max(-20, min(50, $this->currentTemp + $change));
        
        $hour = (int) date('H');
        $dayFactor = sin(($hour - 6) * M_PI / 12) * 3;
        $temp = $this->currentTemp + $dayFactor;
        
        $value = $this->unit === 'FAHRENHEIT' 
            ? ($temp * 9/5) + 32 
            : $temp;
        
        $formattedValue = sprintf("%.1f%s", $value, $this->unit === 'CELSIUS' ? '°C' : '°F');
        
        return [
            'type' => 'temperature',
            'value' => $formattedValue,
        ];
    }

    protected function handleCommand(Command $cmd): CommandResponse
    {
        $action = strtoupper($cmd->getAction());
        $value = $cmd->getValue();

        switch ($action) {
            case 'SET_INTERVAL':
                $interval = (float) $value;
                if ($interval < 1 || $interval > 3600) {
                    return $this->createResponse(false, "Intervalo inválido");
                }
                return $this->createResponse(true, "Intervalo: {$interval}s");

            case 'SET_UNIT':
                if (!in_array($value, ['CELSIUS', 'FAHRENHEIT'])) {
                    return $this->createResponse(false, "Unidade inválida");
                }
                $this->unit = $value;
                return $this->createResponse(true, "Unidade: {$value}");

            case 'GET_READING':
                $reading = $this->generateReading();
                return $this->createResponse(true, "Temperatura: {$reading['value']}");

            case 'GET_STATUS':
                $reading = $this->generateReading();
                $status = json_encode([
                    'name' => $this->name,
                    'value' => $reading['value'],
                    'unit' => $this->unit,
                    'interval' => $this->sendInterval,
                ]);
                return $this->createResponse(true, $status);

            default:
                return $this->createResponse(false, "Comando desconhecido");
        }
    }
}
