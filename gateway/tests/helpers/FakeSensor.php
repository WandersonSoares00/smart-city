<?php

class FakeSensor
{
    public static function sendSensorData(int $port, string $deviceName, string $value)
    {
        $s = new SensorData();
        $s->setDeviceName($deviceName);
        $s->setType("temperature");
        $s->setValue($value);

        $binary = $s->serializeToString();

        $sock = stream_socket_client("udp://127.0.0.1:$port");
        fwrite($sock, $binary);
        fclose($sock);
    }
}
