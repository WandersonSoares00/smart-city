<?php

require __DIR__ . '/protobuf/GPBMetadata/Protobuf/Messages.php';
require __DIR__ . '/protobuf/Command.php';
require __DIR__ . '/protobuf/CommandResponse.php';
require __DIR__ . '/protobuf/DeviceInfo.php';
require __DIR__ . '/protobuf/DeviceList.php';
require __DIR__ . '/protobuf/SensorData.php';

/**
 * Cliente TCP direto para a câmera
 *
 * Uso:
 * php send-command.php <IP> <PORT> <DEVICE_NAME> <ACTION> [VALUE]
 *
 * Ex:
 * php send-command.php 127.0.0.1 9001 Camera-Praca TURN_ON
 * php send-command.php 127.0.0.1 9001 Camera-Praca TAKE_SNAPSHOT
 */

if ($argc < 5) {
    echo "Uso:\n";
    echo "php send-command.php <IP> <PORT> <DEVICE_NAME> <ACTION> [VALUE]\n";
    exit(1);
}

[$_, $ip, $port, $deviceName, $action] = $argv;
$value = $argv[5] ?? '';

echo "Conectando em {$ip}:{$port}...\n";

$socket = @stream_socket_client(
    "tcp://{$ip}:{$port}",
    $errno,
    $errstr,
    5
);

if (!$socket) {
    echo "Erro ao conectar: $errstr ($errno)\n";
    exit(1);
}

// ===============================
// Monta comando Protobuf
// ===============================
$cmd = new Command();
$cmd->setDeviceName($deviceName);
$cmd->setAction($action);
$cmd->setValue($value);

$binary = $cmd->serializeToString();

// ===============================
// Envia comando
// ===============================
fwrite($socket, $binary);

// ===============================
// Lê resposta
// ===============================
$responseData = '';
stream_set_timeout($socket, 5);

while (!feof($socket)) {
    $chunk = fread($socket, 1024);
    if ($chunk === false || $chunk === '') {
        break;
    }
    $responseData .= $chunk;
}

fclose($socket);

if ($responseData === '') {
    echo "Nenhuma resposta recebida.\n";
    exit(1);
}

// ===============================
// Decodifica resposta
// ===============================
$resp = new CommandResponse();

try {
    $resp->mergeFromString($responseData);
} catch (Exception $e) {
    echo "Erro ao decodificar resposta: {$e->getMessage()}\n";
    exit(1);
}

// ===============================
// Exibe resultado
// ===============================
echo "Resposta da câmera:\n";
echo "Device : " . $resp->getDeviceName() . "\n";
echo "Success: " . ($resp->getSuccess() ? 'true' : 'false') . "\n";
echo "Message: " . $resp->getMessage() . "\n";

