<?php

namespace Gateway;

class DeviceRegistry
{
    private $devices = [];

    public function addDevice(Device $device)
    {
        if (isset($this->devices[$device->name])) {
            // Dispositivo já existe, atualiza com novo estado
            $existing = $this->devices[$device->name];
            // Sincroniza propriedades do novo dispositivo
            $existing->currentState = $device->currentState;
            $existing->updateLastSeen();
        } else {
            $this->devices[$device->name] = $device;
        }
    }

    public function getDevice(string $name)
    {
        return $this->devices[$name] ?? null;
    }

    public function updateSensorData(string $sensor, string $type, string $value)
    {
        if (isset($this->devices[$sensor])) {
            $this->devices[$sensor]->$type = $value;
            $this->devices[$sensor]->updateLastSeen();
        }
    }

    public function removeInactiveDevices($timeout = 30): int
    {
        $removed = 0;
        foreach ($this->devices as $name => $device) {
            if (!$device->isActive($timeout)) {
                echo "[REGISTRY] Removendo dispositivo inativo: {$name}\n";
                unset($this->devices[$name]);
                $removed++;
            }
        }
        return $removed;
    }

    public function listDevices(): array
    {
        return array_values(array_map(fn($device) => $device->toArray(), $this->devices));
    }
}
