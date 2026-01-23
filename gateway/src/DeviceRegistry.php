<?php

namespace Gateway;

use PDO;

class DeviceRegistry
{
    private PDO $db;
    private array $cache = [];

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function addDevice(Device $device): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO devices (name, type, ip, port, current_state)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                ip = VALUES(ip),
                port = VALUES(port),
                current_state = VALUES(current_state)
        ");

        $stmt->execute([
            $device->name,
            $device->type,
            $device->ip,
            $device->port,
            $device->currentState
        ]);

        $this->cache[$device->name] = $device;
        echo "[REGISTRY] Dispositivo '{$device->name} ip:{$device->ip} port:{$device->port}' adicionado/atualizado no registro.\n";
    }

    public function getAddr(string $name): ?string
    {
        if (isset($this->cache[$name])) {
            $device = $this->cache[$name];
            return "{$device->ip}:{$device->port}";
        }

        return null;
    }

    public function getDevice(string $name): ?Device
    {
        if (isset($this->cache[$name])) {
            echo "[REGISTRY] Dispositivo '{$name} ip:{$this->cache[$name]->ip}' obtido do cache.\n";
            return $this->cache[$name];
        }

        $stmt = $this->db->prepare("SELECT * FROM devices
                                            WHERE name = ?
                                            ORDER BY id DESC 
                                            LIMIT 1");
        $stmt->execute([$name]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (! $data) {
            return null;
        }

        $device = new Device(
            $data['name'],
            $data['type'],
            $data['ip'],
            (int) $data['port'],
            $data['current_state']
        );

        $device->id = (int) $data['id'];
        $device->current_state = $data['current_state'];
        $device->state_value  = $data['state_value'];

        $this->cache[$name] = $device;

        return $device;
    }

    public function updateSensorData(string $deviceName, string $state, string $value): void
    {
        $stmt = $this->db->prepare("
            UPDATE devices
            SET current_state = ?, state_value = ?
            WHERE name = ?
        ");

        $stmt->execute([$state, $value, $deviceName]);

        if (isset($this->cache[$deviceName])) {
            $this->cache[$deviceName]->current_state = $state;
            $this->cache[$deviceName]->state_value = $value;
        }
    }

    public function listDevices(): array
    {
        return $this->db
            ->query("
                SELECT
                    id,
                    name,
                    type,
                    location,
                    ip,
                    port,
                    current_state,
                    state_value,
                    created_at
                FROM devices
            ")
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}