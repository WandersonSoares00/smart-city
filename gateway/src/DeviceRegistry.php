<?php

namespace Gateway;

class DeviceRegistry
{
    private $devices = [];

    public function addDevice(Device $device)
    {
        $this->devices[$device->name] = $device;
    }

    public function getDevice(string $name)
    {
        return $this->devices[$name] ?? null;
    }

    public function updateSensorData(string $sensor, string $type, string $value)
    {
        if (isset($this->devices[$sensor])) {
            $this->devices[$sensor]->$type = $value;
        }
    }

    public function listDevices(): array
    {
        return array_map(fn($device) => $device->toArray(), $this->devices);
    }
}
