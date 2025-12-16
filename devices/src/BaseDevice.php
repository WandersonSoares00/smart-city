<?php

namespace Devices;

use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Datagram\Factory;
use React\Socket\SocketServer;
use DeviceInfo;
use Command;
use CommandResponse;

/**
 * Classe base para todos os dispositivos da cidade inteligente.
 * 
 * Responsabilidades:
 * - Descoberta via multicast UDP
 * - Servidor TCP para receber comandos
 * - Envio de informações do dispositivo
 */
abstract class BaseDevice
{
    protected LoopInterface $loop;
    protected string $name;
    protected string $type;
    protected string $ip;
    protected int $port;
    protected string $currentState;

    protected string $multicastGroup;
    protected int $multicastPort;
    protected int $gatewayResponsePort;

    public function __construct(string $name, string $type, int $port, string $initialState = 'OFF')
    {
        $this->loop = Loop::get();
        $this->name = $name;
        $this->type = $type;
        $this->ip = $this->getLocalIp();
        $this->port = $port;
        $this->currentState = $initialState;

        $this->multicastGroup = $_ENV['MULTICAST_GROUP'] ?? '239.0.0.1';
        $this->multicastPort = (int) ($_ENV['MULTICAST_PORT'] ?? 5000);
        $this->gatewayResponsePort = (int) ($_ENV['RESPONSE_PORT'] ?? 5000);
    }

    /**
     * Obtém o IP local da máquina
     */
    protected function getLocalIp(): string
    {
        return $_ENV['LOCAL_IP'] ?? gethostbyname(gethostname()) ?? '127.0.0.1';
    }

    /**
     * Inicia o dispositivo
     */
    public function start(): void
    {
        $this->listenForDiscovery();
        $this->startTcpServer();
        $this->onStart();

        $this->loop->run();
    }

    /**
     * Hook para ações específicas ao iniciar
     */
    protected function onStart(): void
    {
        // Pode ser sobrescrito pelas classes filhas
    }

    /**
     * Escuta mensagens de descoberta multicast do Gateway
     */
    protected function listenForDiscovery(): void
    {
        // Envia informações imediatamente ao iniciar
        $this->sendDeviceInfo();
        
        // Envia heartbeat a cada 10 segundos para manter o dispositivo ativo
        $this->loop->addPeriodicTimer(10, function () {
            $this->sendDeviceInfo();
        });
    }

    /**
     * Envia informações do dispositivo para o Gateway
     */
    protected function sendDeviceInfo(): void
    {
        $info = new DeviceInfo();
        $info->setName($this->name);
        $info->setType($this->type);
        $info->setIp($this->ip);
        $info->setPort($this->port);
        $info->setCurrentState($this->currentState);

        $binary = $info->serializeToString();
        // Envia resposta diretamente para o gateway (localhost em desenvolvimento)
        $gatewayHost = $_ENV['GATEWAY_HOST'] ?? '127.0.0.1';
        $address = "{$gatewayHost}:{$this->gatewayResponsePort}";

        $sock = @stream_socket_client("udp://{$address}", $errno, $errstr);
        if ($sock) {
            fwrite($sock, $binary);
            fclose($sock);
        }
    }

    /**
     * Inicia servidor TCP para receber comandos
     */
    protected function startTcpServer(): void
    {
        $server = new SocketServer("0.0.0.0:{$this->port}", [], $this->loop);

        $server->on('connection', function ($conn) {
            $conn->on('data', function ($data) use ($conn) {
                $cmd = new Command();
                
                try {
                    $cmd->mergeFromString($data);
                } catch (\Exception $e) {
                    echo "[{$this->type}] Erro ao decodificar comando\n";
                    return;
                }
                $response = $this->handleCommand($cmd);
                $conn->write($response->serializeToString());
                
                // Envia heartbeat imediatamente após processar comando para atualizar estado
                $this->sendDeviceInfo();
            });
        });
    }

    /**
     * Processa um comando - implementado pelas classes filhas
     */
    abstract protected function handleCommand(Command $cmd): CommandResponse;

    /**
     * Cria uma resposta de comando
     */
    protected function createResponse(bool $success, string $message): CommandResponse
    {
        $response = new CommandResponse();
        $response->setDeviceName($this->name);
        $response->setSuccess($success);
        $response->setMessage($message);
        return $response;
    }
}
