<?php

namespace Gateway;

use DeviceInfo;
use React\EventLoop\Loop;
use React\Datagram\Factory;
use React\Socket\SocketServer;
use SensorData;
use Command;
use CommandResponse;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;

class Gateway
{
    private $loop;
    private DeviceRegistry $deviceRegistry;
    private $group;
    private $port;
    private $response_port;
    
    public function __construct()
    {
        $this->loop = Loop::get();
        $this->deviceRegistry = new DeviceRegistry();
        $this->group = $_ENV['MULTICAST_GROUP'];
        $this->port = $_ENV['MULTICAST_PORT'];
        $this->response_port = $_ENV['RESPONSE_PORT'];
    }

    public function start(): void
    {
        echo "Starting Gateway...\n";

        $this->startPeriodicDiscovery();
        $this->startDiscoveryResponseListener();
        $this->startSensorUdpListener();
        $this->startTcpServer();
        $this->startInactiveDeviceCleanup();

        echo "[GATEWAY] Loop ativo...\n";

        $this->loop->run();
    }

    private function startPeriodicDiscovery() : void
    {
        $this->loop->addPeriodicTimer(5, function () {
            echo "[DISCOVERY] Broadcast multicast para {$this->group}:{$this->port}...\n";
            BroadcastDiscovery::send($this->group, $this->port, "DISCOVERY");
        });
    }

    private function startInactiveDeviceCleanup(): void
    {
        // Remove dispositivos inativos a cada 5 segundos (mais responsivo)
        $this->loop->addPeriodicTimer(5, function () {
            // Considera inativo após 15 segundos sem heartbeat
            $removed = $this->deviceRegistry->removeInactiveDevices(15);
            if ($removed > 0) {
                echo "[CLEANUP] {$removed} dispositivo(s) inativo(s) removido(s)\n";
            }
        });
    }

    private function startDiscoveryResponseListener(): void
    {
        $factory = new Factory($this->loop);

        $factory->createServer("0.0.0.0:{$this->response_port}")->then(function ($server) {

            echo "[DISCOVERY] Aguardando respostas dos dispositivos...\n";

            $server->on('message', function ($msg, $addr) {

                echo "[DISCOVERY] Resposta recebida de $addr - $msg \n";

                if ($msg === "DISCOVERY") {
                    return;
                }
                
                // Decodifica Protobuf
                try {
                    $info = new DeviceInfo();
                    $info->mergeFromString($msg);
                } catch (\Exception $e) {
                    return;
                }

                $device = new Device(
                    name: $info->getName(),
                    type: $info->getType(),
                    ip:   $info->getIp(),
                    port: $info->getPort(),
                    currentState: $info->getCurrentState()
                );

                $this->deviceRegistry->addDevice($device);

                echo "[DISCOVERY] Dispositivo registrado: {$device}\n";
            });
        });
    }

    private function startSensorUdpListener(): void
    {
        $port = (int) $_ENV["SENSOR_UDP_PORT"];
        $factory = new Factory($this->loop);

        $factory->createServer("0.0.0.0:$port")->then(function($server) {
            echo "[UDP] Escutando sensores na porta {$_ENV["SENSOR_UDP_PORT"]}\n";

            $server->on('message', function($msg, $addr) {
                echo "[UDP] Mensagem recebida de $addr → " . strlen($msg) . " bytes\n";
                
                try {
                    $info = new SensorData();
                    $info->mergeFromString($msg);
                    
                    $this->deviceRegistry->updateSensorData($info->getDeviceName(), $info->getType(), $info->getValue());
                } catch (\Exception $e) {
                    echo "[ERROR] Falha ao processar mensagem UDP: " . $e->getMessage() . "\n";
                }
            });
        });
    }

    private function sendCommandToDevice(Device $device, Command $cmd, callable $onResponse): void {
        
        $connector = new Connector($this->loop);
        $address = "{$device->ip}:{$device->port}";

        $connector->connect($address)->then(
            function (ConnectionInterface $deviceConn) use ($cmd, $onResponse) {

                $deviceConn->write($cmd->serializeToString());

                $deviceConn->on('data', function ($data) use ($onResponse) {

                    $resp = new CommandResponse();
                    try {
                        $resp->mergeFromString($data);
                    } catch (\Exception $e) {
                        $onResponse("Erro ao decodificar resposta do dispositivo");
                        return;
                    }

                    $onResponse($resp);
                });
            },
            function () use ($device, $onResponse) {

                $resp = new CommandResponse();
                $resp->setDeviceName($device->name);
                $resp->setSuccess(false);
                $resp->setMessage("Falha ao conectar ao dispositivo.");

                $onResponse($resp);
            }
        );
    }

    private function startTcpServer(): void
    {
        $port = (int) $_ENV["GATEWAY_TCP_PORT"];

        $server = new SocketServer("0.0.0.0:$port", [], $this->loop);

        echo "[TCP] Servidor TCP na porta $port\n";

        $server->on('connection', function ($conn) {
            echo "[TCP] Cliente conectado.\n";

            $conn->on('data', function ($data) use ($conn) {
                $cmd = trim($data);
                
                echo "[TCP] Comando recebido: '{$cmd}'\n";

                if ($cmd === "LIST") {
                    $devices = $this->deviceRegistry->listDevices();
                    $json = json_encode($devices, JSON_PRETTY_PRINT) . "\n";
                    echo "[TCP] Enviando lista com " . count($devices) . " dispositivo(s)\n";
                    $conn->write($json);
                    return;
                }

                $parts = explode(" ", $cmd);

                // Comandos genéricos: CMD <device> <action> [value]
                // Exemplos:
                // CMD Poste-Rua-1 TURN_ON
                // CMD Poste-Rua-1 SET_BRIGHTNESS 80
                // CMD Semaforo-Centro SET_MODE AUTO
                // CMD Camera-Praca SET_RESOLUTION 4K
                // CMD Temp-Centro SET_INTERVAL 30

                if (count($parts) >= 3 && $parts[0] === "CMD") {
                    $deviceName = $parts[1];
                    $action = $parts[2];
                    $value = $parts[3] ?? "";

                    $device = $this->deviceRegistry->getDevice($deviceName);

                    if (!$device) {
                        $conn->write("ERROR: device '$deviceName' not found.\n");
                        return;
                    }

                    $command = new Command();
                    $command->setDeviceName($deviceName);
                    $command->setAction($action);
                    $command->setValue($value);

                    $this->sendCommandToDevice($device, $command, function (CommandResponse $resp) use ($conn) {
                        $conn->write(json_encode([
                            "device"  => $resp->getDeviceName(),
                            "success" => $resp->getSuccess(),
                            "message" => $resp->getMessage()
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
                    });

                    return;
                }

                // SET LIGHT <device> <red|yellow|green> - mantido para compatibilidade
                if (count($parts) >= 4 && $parts[0] === "SET" && $parts[1] === "LIGHT") {

                    $deviceName = $parts[2];
                    $color = $parts[3];

                    $device = $this->deviceRegistry->getDevice($deviceName);

                    if (!$device) {
                        $conn->write("ERROR: device '$deviceName' not found.\n");
                        return;
                    }

                    $command = new Command();
                    $command->setDeviceName($deviceName);
                    $command->setAction("SET_LIGHT");
                    $command->setValue($color);

                    $this->sendCommandToDevice($device, $command, function (CommandResponse $resp) use ($conn) {
                        $conn->write(json_encode([
                            "device"  => $resp->getDeviceName(),
                            "success" => $resp->getSuccess(),
                            "message" => $resp->getMessage()
                        ]) . "\n");
                    });

                    return;
                }

                // Comando HELP - lista comandos disponíveis
                if ($cmd === "HELP") {
                    $help = <<<HELP
╔═══════════════════════════════════════════════════════════════════╗
║                    COMANDOS DISPONÍVEIS                           ║
╠═══════════════════════════════════════════════════════════════════╣
║ LIST                           - Lista todos os dispositivos      ║
║ CMD <device> <action> [value]  - Envia comando para dispositivo   ║
║ SET LIGHT <device> <color>     - Define cor do semáforo           ║
║ HELP                           - Mostra esta ajuda                ║
╠═══════════════════════════════════════════════════════════════════╣
║                    EXEMPLOS DE COMANDOS                           ║
╠═══════════════════════════════════════════════════════════════════╣
║ POSTE:                                                            ║
║   CMD Poste-Rua-1 TURN_ON                                         ║
║   CMD Poste-Rua-1 TURN_OFF                                        ║
║   CMD Poste-Rua-1 SET_BRIGHTNESS 80                               ║
║   CMD Poste-Rua-1 SET_MODE AUTO                                   ║
╠═══════════════════════════════════════════════════════════════════╣
║ SEMÁFORO:                                                         ║
║   CMD Semaforo-Centro SET_MODE AUTO                               ║
║   CMD Semaforo-Centro SET_MODE MANUAL                             ║
║   CMD Semaforo-Centro SET_LIGHT RED                               ║
║   CMD Semaforo-Centro SET_TIME RED:20                             ║
╠═══════════════════════════════════════════════════════════════════╣
║ CÂMERA:                                                           ║
║   CMD Camera-Praca TURN_ON                                        ║
║   CMD Camera-Praca SET_RESOLUTION 4K                              ║
║   CMD Camera-Praca SET_FPS 60                                     ║
║   CMD Camera-Praca SET_PTZ 90:45:2                                ║
║   CMD Camera-Praca SET_NIGHT_VISION ON                            ║
╠═══════════════════════════════════════════════════════════════════╣
║ SENSORES:                                                         ║
║   CMD Temp-Centro GET_READING                                     ║
║   CMD Temp-Centro SET_INTERVAL 30                                 ║
║   CMD AirQuality-Centro GET_DETAILED                              ║
║   CMD Noise-Centro SET_ALERT_THRESHOLD 80                         ║
╚═══════════════════════════════════════════════════════════════════╝
HELP;
                    $conn->write($help . "\n");
                    return;
                }

                $conn->write("Comando inválido. Digite HELP para ver comandos disponíveis.\n");            });
        });
    }
}
