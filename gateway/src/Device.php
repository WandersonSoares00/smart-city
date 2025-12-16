<?php

namespace Gateway;

class Device
{
    private $name;
    private $type;
    private $ip;
    private $port;
    private $currentState;
    private $lastSeen;

    private $sensorData = [];
    public function __construct($name, $type, $ip, $port, $currentState)
    {
        $this->name = $name;
        $this->type = $type;
        $this->ip = $ip;
        $this->port = $port;
        $this->currentState = $currentState;
        $this->sensorData = [];
        $this->lastSeen = time();
    }

    public function __set($property, $value)
    {
        if ($property === 'currentState') {
            $this->currentState = $value;
            return;
        }
        
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        $this->sensorData[$property] = $value;
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        return null;
    }

    public function __toString()
    {
        return sprintf(
            "Device(Name: %s, Type: %s, IP: %s, Port: %d, State: %s)",
            $this->name,
            $this->type,
            $this->ip,
            $this->port,
            $this->currentState
        );
    }

    public function updateLastSeen()
    {
        $this->lastSeen = time();
    }

    public function getLastSeen()
    {
        return $this->lastSeen;
    }

    public function isActive($timeout = 30)
    {
        return (time() - $this->lastSeen) < $timeout;
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'ip' => $this->ip,
            'port' => $this->port,
            'currentState' => $this->currentState,
            'sensorData'   => $this->sensorData,
        ];
    }
}