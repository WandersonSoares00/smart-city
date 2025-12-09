<?php

namespace Gateway;

use DeviceInfo;
use React\EventLoop\Loop;
use React\Datagram\Factory;
use React\Socket\SocketServer;
use SensorData;

class Gateway
{
    private $loop;
    private DeviceRegistry $deviceRegistry;
    private $group;
    private $port;
    
    public function __construct()
    {
        $this->loop = Loop::get();
        $this->deviceRegistry = new DeviceRegistry();
        $this->group = getenv('MULTICAST_GROUP');
        $this->port = (int)getenv('MULTICAST_PORT');
    }

    public function start(): void
    {
        echo "Starting Gateway...\n";

        $this->startPeriodicDiscovery();
        $this->startDiscoveryResponseListener();
        $this->startSensorUdpListener();
        $this->startTcpServer();

        echo "[GATEWAY] Loop ativo...\n";

        $this->loop->run();
    }

    private function startPeriodicDiscovery() : void
    {
        $this->loop->addPeriodicTimer(5, function () {
            echo "[DISCOVERY] Broadcast multicast...\n";
            MulticastDiscovery::send($this->group, $this->port, "DISCOVERY");
        });
    }

    private function startDiscoveryResponseListener(): void
    {
        $factory = new Factory($this->loop);

        $factory->createServer("0.0.0.0:" . $this->port)->then(function ($server) {

            $server->joinGroup($this->group);

            echo "[DISCOVERY] Aguardando respostas dos dispositivos...\n";

            $server->on('message', function ($msg, $addr) {

                echo "[DISCOVERY] Resposta recebida de $addr\n";

                list($ip, $port) = explode(":", $addr);

                // Decodifica Protobuf
                $info = new DeviceInfo();
                $info->mergeFromString($msg);

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
                echo "[UDP] Mensagem recebida de $addr → $msg\n";
                //list($ip, ) = explode(":", $addr);
                $info = new SensorData();
                $info->mergeFromString($msg);
                
                $this->deviceRegistry->updateSensorData($info->getDeviceName(), $info->getType(), $info->getValue());
            });
        });
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

                if ($cmd === "LIST") {
                    $conn->write(json_encode($this->deviceRegistry->listDevices(), JSON_PRETTY_PRINT));
                } else {
                    $conn->write("Comando inválido\n");
                }
            });
        });
    }
}
