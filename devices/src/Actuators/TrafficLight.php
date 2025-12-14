<?php

namespace Devices\Actuators;

use Devices\BaseDevice;
use Command;
use CommandResponse;

/**
 * Semáforo Inteligente
 * 
 * Comandos: SET_LIGHT, SET_MODE, GET_STATUS
 */
class TrafficLight extends BaseDevice
{
    private string $currentLight = 'RED';
    private string $mode = 'AUTO';
    private array $phaseTimes = ['RED' => 15, 'YELLOW' => 3, 'GREEN' => 12];
    private $cycleTimer = null;

    public function __construct(string $name, int $port)
    {
        parent::__construct($name, 'TRAFFIC_LIGHT', $port, 'RED');
    }

    protected function onStart(): void
    {
        if ($this->mode === 'AUTO') {
            $this->startAutoCycle();
        }
    }

    private function startAutoCycle(): void
    {
        if ($this->cycleTimer) {
            $this->loop->cancelTimer($this->cycleTimer);
        }

        $cycle = ['GREEN', 'YELLOW', 'RED'];
        $position = array_search($this->currentLight, $cycle);

        $this->scheduleNextPhase($cycle, $position);
    }

    private function scheduleNextPhase(array $cycle, int $position): void
    {
        if ($this->mode !== 'AUTO') return;

        $phase = $cycle[$position];
        $duration = $this->phaseTimes[$phase];

        $this->cycleTimer = $this->loop->addTimer($duration, function () use ($cycle, $position) {
            $nextPosition = ($position + 1) % count($cycle);
            $this->currentLight = $cycle[$nextPosition];
            $this->currentState = $this->currentLight;
            $this->scheduleNextPhase($cycle, $nextPosition);
        });
    }

    protected function handleCommand(Command $cmd): CommandResponse
    {
        $action = strtoupper($cmd->getAction());
        $value = $cmd->getValue();

        switch ($action) {
            case 'SET_LIGHT':
                if ($this->mode !== 'MANUAL') {
                    return $this->createResponse(false, "Modo MANUAL necessário");
                }
                if (!in_array($value, ['RED', 'YELLOW', 'GREEN'])) {
                    return $this->createResponse(false, "Cor inválida");
                }
                $this->currentLight = $value;
                $this->currentState = $value;
                return $this->createResponse(true, "Luz: {$value}");

            case 'SET_MODE':
                $this->mode = strtoupper($value);
                if ($this->mode === 'AUTO') {
                    $this->startAutoCycle();
                }
                return $this->createResponse(true, "Modo: {$this->mode}");

            case 'SET_TIME':
                $parts = explode(':', $value);
                if (count($parts) === 2) {
                    $phase = strtoupper($parts[0]);
                    $time = (int) $parts[1];
                    if (isset($this->phaseTimes[$phase]) && $time > 0) {
                        $this->phaseTimes[$phase] = $time;
                        if ($this->mode === 'AUTO') {
                            $this->startAutoCycle();
                        }
                        return $this->createResponse(true, "{$phase}: {$time}s");
                    }
                }
                return $this->createResponse(false, "Formato: FASE:TEMPO");

            case 'GET_STATUS':
                $status = json_encode([
                    'name' => $this->name,
                    'light' => $this->currentLight,
                    'mode' => $this->mode,
                    'times' => $this->phaseTimes,
                ]);
                return $this->createResponse(true, $status);

            default:
                return $this->createResponse(false, "Comando desconhecido");
        }
    }
}
