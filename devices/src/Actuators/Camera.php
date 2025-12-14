<?php

namespace Devices\Actuators;

use Devices\BaseDevice;
use Command;
use CommandResponse;

/**
 * Câmera de Vigilância
 * 
 * Comandos: TURN_ON, TURN_OFF, SET_RESOLUTION, SET_FPS, GET_STATUS
 */
class Camera extends BaseDevice
{
    private bool $isRecording = false;
    private string $resolution = 'HD';
    private int $fps = 30;

    private const VALID_RESOLUTIONS = ['SD', 'HD', 'FULL_HD', '4K'];
    private const VALID_FPS = [15, 24, 30, 60];

    public function __construct(string $name, int $port)
    {
        parent::__construct($name, 'CAMERA', $port, 'OFF');
    }

    protected function handleCommand(Command $cmd): CommandResponse
    {
        $action = strtoupper($cmd->getAction());
        $value = $cmd->getValue();

        switch ($action) {
            case 'TURN_ON':
                $this->isRecording = true;
                $this->currentState = "RECORDING:{$this->resolution}@{$this->fps}fps";
                return $this->createResponse(true, "Gravando em {$this->resolution}@{$this->fps}fps");

            case 'TURN_OFF':
                $this->isRecording = false;
                $this->currentState = 'OFF';
                return $this->createResponse(true, "Desligada");

            case 'SET_RESOLUTION':
                $res = strtoupper($value);
                if (!in_array($res, self::VALID_RESOLUTIONS)) {
                    return $this->createResponse(false, "Resolução inválida");
                }
                $this->resolution = $res;
                if ($this->isRecording) {
                    $this->currentState = "RECORDING:{$this->resolution}@{$this->fps}fps";
                }
                return $this->createResponse(true, "Resolução: {$res}");

            case 'SET_FPS':
                $f = (int) $value;
                if (!in_array($f, self::VALID_FPS)) {
                    return $this->createResponse(false, "FPS inválido");
                }
                $this->fps = $f;
                if ($this->isRecording) {
                    $this->currentState = "RECORDING:{$this->resolution}@{$this->fps}fps";
                }
                return $this->createResponse(true, "FPS: {$f}");

            case 'GET_STATUS':
                $status = json_encode([
                    'name' => $this->name,
                    'recording' => $this->isRecording,
                    'resolution' => $this->resolution,
                    'fps' => $this->fps,
                    'state' => $this->currentState,
                ]);
                return $this->createResponse(true, $status);

            default:
                return $this->createResponse(false, "Comando desconhecido");
        }
    }
}
