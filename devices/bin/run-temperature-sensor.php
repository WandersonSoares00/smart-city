<?php

/**
 * Script para executar o Sensor de Temperatura
 * 
 * Uso: php bin/run-temperature-sensor.php [nome] [porta] [intervalo]
 * 
 * Exemplo: php bin/run-temperature-sensor.php "Temp-Centro" 7104 15
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Devices\Sensors\TemperatureSensor;

// Carrega variáveis de ambiente
$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

// Parâmetros
$name = $argv[1] ?? 'TempSensor-' . mt_rand(1000, 9999);
$port = (int) ($argv[2] ?? 7104);
$interval = (float) ($argv[3] ?? 15.0);

echo "============================================\n";
echo "🌡️  SENSOR DE TEMPERATURA - CIDADE INTELIGENTE\n";
echo "============================================\n";
echo "Nome: {$name}\n";
echo "Porta TCP: {$port}\n";
echo "Intervalo: {$interval}s\n";
echo "============================================\n\n";

$device = new TemperatureSensor($name, $port, $interval);
$device->start();
