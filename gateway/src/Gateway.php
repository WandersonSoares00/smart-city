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

        $consumer = new SensorConsumer();
        $consumer->start($this->deviceRegistry, $this->loop);

        $this->startSensorUdpListener();
        $this->startTcpServer();
        //$this->startInactiveDeviceCleanup();

        echo "[GATEWAY] Loop ativo...\n";

        $this->loop->run();
    }

    private function startPeriodicDiscovery() : void
    {
        $this->loop->addPeriodicTimer(5, function () {
            echo "[DISCOVERY] Broadcast para {$this->group}:{$this->port}...\n";
            BroadcastDiscovery::send($this->group, $this->port, "DISCOVERY");
        });
    }

    /*
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
    */

    private function  startDiscoveryResponseListener(): void
    {
        $factory = new Factory($this->loop);

        $factory->createServer("0.0.0.0:{$this->response_port}")->then(function ($server) {

            echo "[DISCOVERY] Aguardando respostas dos dispositivos na porta {$this->response_port}...\n";

            $server->on('message', function ($msg, $addr) {

                echo "[DISCOVERY] Resposta recebida de $addr - $msg \n";
                
                $realIp = trim(parse_url("tcp://$addr", PHP_URL_HOST));

                echo "[DISCOVERY] Pacote recebido de $addr (IP Real: $realIp)\n";
                
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
                    
                    $this->deviceRegistry->updateSensorData($info->getDeviceName(), $info->getValue(), $info->getValue());
                } catch (\Exception $e) {
                    echo "[ERROR] Falha ao processar mensagem UDP: " . $e->getMessage() . "\n";
                }
            });
        });
    }
    
    /*
    private function sendCommandToDevice(string $deviceAddr, Command $cmd, callable $onResponse): void {
        
        $address = "tcp://{$deviceAddr}";

        echo "\n--- DEBUG START ---\n";
        echo "Tentando conectar em: [{$address}]\n"; // Os colchetes mostram se tem espaço escondido
        
        // 2. Tenta conectar SEM o @ para ver warnings no console e com timeout explícito
        // Timeout de 3 segundos para teste
        $socket = stream_socket_client($address);

        // Se chegou aqui, escreve
        fwrite($socket, $cmd->serializeToString());
        echo "Dado escrito no socket.\n";
        
        // ... resto do código de leitura ...
        fclose($socket);
    }
    */

    /*
    private function sendCommandToDevice(string $deviceAddr, Command $cmd, callable $onResponse): void {
        
        
        //$connector = new Connector($this->loop);
        $address = "{$device->ip}:{$device->port}";
        echo "Connecting to $address\n";

        $socket = @stream_socket_client("tcp://{$address}", $errno, $errstr);

        if (!$socket) {
            $resp = new CommandResponse();
            $resp->setDeviceName($device->name);
            $resp->setSuccess(false);
            $resp->setMessage("Falha ao conectar ao dispositivo: $errstr ($errno).");

            $onResponse($resp);
            return;
        }

        fwrite($socket, $cmd->serializeToString());

        $data = "";
        stream_set_timeout($socket, 5);

        while (!feof($socket)) {
            $chunk = fread($socket, 8192);
            if ($chunk === false || $chunk === "") {
                break;
            }
            $data .= $chunk;
        }

        fclose($socket);

        $resp = new CommandResponse();
        
        try {
            $resp->mergeFromString($data);
            echo "Response decoded: " . $resp . "\n";
        } catch (\Exception $e) {
            $onResponse("Erro ao decodificar resposta do dispositivo");
            return;
        }

        $onResponse($resp);

        echo "Sent command to device\n";

        return; 
        $connector = new Connector($this->loop);
        $address = "tcp://{$deviceAddr}";
        
        $connector->connect($address)->then(
            function (ConnectionInterface $deviceConn) use ($cmd, $onResponse) {
                echo "Connected " . $cmd . "\n";
                
                $deviceConn->write($cmd->serializeToString());

                $deviceConn->on('data', function ($data) use ($onResponse) {
                    echo "Response received: " . strlen($data) . " bytes\n";
                    $resp = new CommandResponse();
                    try {
                        $resp->mergeFromString($data);
                        echo "Response decoded: " . $resp . "\n";
                    } catch (\Exception $e) {
                        $onResponse("Erro ao decodificar resposta do dispositivo");
                        return;
                    }

                    $onResponse($resp);
                });
            },
            function () use (CommandResponse $resp, $onResponse) {
                $onResponse($resp);
            }
        );
    }   */

    private function sendCommandToDevice(string $deviceAddr, Command $cmd, callable $onResponse): void 
    {
        $connector = new Connector(['timeout' => 3.0], $this->loop);
        $address = "tcp://{$deviceAddr}";

        echo "[ASYNC] Connecting to $address\n";

        $connector->connect($address)->then(
            function (ConnectionInterface $conn) use ($cmd, $onResponse) {
                
                $conn->write($cmd->serializeToString());

                $buffer = '';

                $timer = $this->loop->addTimer(5.0, function () use ($conn) {
                    echo "[TIMEOUT] Fechando conexão por inatividade.\n";
                    $conn->close();
                });

                $conn->on('data', function ($chunk) use (&$buffer) {
                    $buffer .= $chunk;
                });

                $conn->on('close', function () use (&$buffer, $onResponse, $timer, $cmd) {
                    $this->loop->cancelTimer($timer);

                    $resp = new CommandResponse();
                    
                    try {
                        if (empty($buffer)) {
                            throw new \Exception("Conexão fechada sem dados.");
                        }

                        $resp->mergeFromString($buffer);
                        echo "[ASYNC] Response decoded: Success\n";
                        $onResponse($resp);

                    } catch (\Exception $e) {
                        $errResp = new CommandResponse();
                        $errResp->setDeviceName($cmd->getDeviceName());
                        $errResp->setSuccess(false);
                        $errResp->setMessage("Erro ao ler resposta: " . $e->getMessage());
                        $onResponse($errResp);
                    }
                });
            },
            function (\Exception $e) use ($cmd, $onResponse) {
                $resp = new CommandResponse();
                $resp->setDeviceName($cmd->getDeviceName());
                $resp->setSuccess(false);
                $resp->setMessage("Falha ao conectar (Async): " . $e->getMessage());
                
                $onResponse($resp);
            }
        );
    }

    private function startTcpServer(): void
    {
        $port = (int) $_ENV["GATEWAY_TCP_PORT"];

        $server = new SocketServer("0.0.0.0:$port", [], $this->loop);

        $dynamo = new DynamoLogger();

        $server->on('connection', function ($conn) use ($dynamo) {
            $conn->on('data', function ($data) use ($conn, $dynamo) {
                $cmd = trim($data);

                if ($cmd === "LIST") {
                    $devices = $this->deviceRegistry->listDevices();
                    $json = json_encode($devices, JSON_PRETTY_PRINT) . "\n";
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

                    $deviceAddr = $this->deviceRegistry->getAddr($deviceName);
                    if (!$deviceAddr) {
                        $conn->write("ERROR: device '$deviceName' not found.\n");
                        return;
                    }

                    $command = new Command();
                    $command->setDeviceName($deviceName);
                    $command->setAction($action);
                    $command->setValue($value);
                    echo "[COMMAND] Enviando comando para {$deviceName}: {$action} {$value}\n";

                    $this->sendCommandToDevice($deviceAddr, $command, function (CommandResponse $resp) use ($action, $conn) {
                        echo $resp->getSuccess() ? "[COMMAND] Comando executado com sucesso em {$resp->getDeviceName()}\n" : "[COMMAND] Falha ao executar comando em {$resp->getDeviceName()}: {$resp->getMessage()}\n";

                        $conn->write(json_encode([
                            "device"  => $resp->getDeviceName(),
                            "success" => $resp->getSuccess(),
                            "message" => $resp->getMessage(),
                            "state"   => $resp->getState()
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");

                        echo "[COMMAND] Resposta enviada ao cliente.\n";

                        if ($action === "TAKE_SNAPSHOT" && $resp->getSuccess()) {
                            echo "[SNAPSHOT] Processando imagem da câmera...\n";

                            $url = $resp->getMessage();

                            try {
                                $imageData = $this->downloadImage($url);

                                try {
                                    $minio = new MinioClient();
                                    $filename = sprintf("%s_%d.jpg", $resp->getDeviceName(), time());
                                    
                                    $minioUrl = $minio->upload($filename, $imageData);

                                    echo "[SNAPSHOT] Imagem salva em MinIO: $minioUrl\n";
                                    
                                } catch (\Exception $e) {
                                    echo "[SNAPSHOT] Falha ao enviar imagem para MinIO: " . $e->getMessage() . "\n";
                                }

                            } catch (\Exception $e) {
                                $conn->write("ERROR: " . $e->getMessage() . "\n");
                            }
                        }
                    
                    });

                    try {
                        $dynamo->logCommand([
                            'device'    => $deviceName,
                            'timestamp' => (new \DateTime())->format(DATE_ATOM),
                            'source'    => 'frontend',
                            'action'    => $action,
                            'value'     => $value,
                            'status'    => 'SENT',
                            'metadata'  => null
                        ]);
                    } catch (\Exception $e) {
                        echo "[DYNAMO] Falha ao registrar comando: " . $e->getMessage() . "\n";
                    }

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
                            "message" => $resp->getMessage(),
                            "state"   => $resp->getState()
                        ]) . "\n");
                    });
                    
                    try {
                        $dynamo->logCommand([
                            'device'    => $deviceName,
                            'timestamp' => (new \DateTime())->format(DATE_ATOM),
                            'source'    => 'frontend',
                            'action'    => 'SET',
                            'value'     => $color,
                            'status'    => 'SENT',
                            'metadata'  => null
                        ]);
                    } catch (\Exception $e) {
                        echo "[DYNAMO] Falha ao registrar comando: " . $e->getMessage() . "\n";
                    }

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

                $conn->write(json_encode([
                    'status'  => 'error',
                    'error'   => 'INVALID_COMMAND',
                    'message' => 'Comando inválido',
                    'help'    => ['LIST', 'COMMAND', 'STATUS']
                ], JSON_UNESCAPED_UNICODE) . "\n");
            });
        });
    }

    private function downloadImage(string $url): string
    {
        $context = stream_context_create([
            'http' => ['timeout' => 5]
        ]);
    
        $data = @file_get_contents($url, false, $context);
    
        if ($data === false) {
            throw new \RuntimeException("Falha ao baixar imagem");
        }
    
        return $data;
    }
}
