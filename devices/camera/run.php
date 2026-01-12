<?php

/**
 * Script para executar a Câmera de Vigilância
 * 
 * Uso: php bin/run-camera.php [nome] [porta]
 * 
 * Exemplo: php bin/run-camera.php "Camera-Praca-Central" 7103
 */

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Devices\Actuators\Camera;

// Carrega variáveis de ambiente
$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->safeLoad(); 

// Parâmetros
$name = $argv[1] ?? 'Camera-' . mt_rand(1000, 9999);
$port = (int) ($argv[2] ?? 7103);

echo "============================================\n";
echo "📹 CÂMERA DE VIGILÂNCIA - CIDADE INTELIGENTE\n";
echo "============================================\n";
echo "Nome: {$name}\n";
echo "Porta TCP: {$port}\n";
echo "============================================\n\n";

$device = new Camera($name, $port);
$device->start();
