<?php

/**
 * Script para executar o Semáforo
 * 
 * Uso: php bin/run-traffic-light.php [nome] [porta]
 * 
 * Exemplo: php bin/run-traffic-light.php "Semaforo-Av-Principal" 7102
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Devices\Actuators\TrafficLight;

// Carrega variáveis de ambiente
$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

// Parâmetros
$name = $argv[1] ?? 'TrafficLight-' . mt_rand(1000, 9999);
$port = (int) ($argv[2] ?? 7102);

echo "============================================\n";
echo "🚦 SEMÁFORO - CIDADE INTELIGENTE\n";
echo "============================================\n";
echo "Nome: {$name}\n";
echo "Porta TCP: {$port}\n";
echo "============================================\n\n";

$device = new TrafficLight($name, $port);
$device->start();
