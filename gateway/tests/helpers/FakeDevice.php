<?php

class FakeDevice
{
    public static function sendDeviceInfo(string $group, int $port, string $name)
    {
        $d = new DeviceInfo();
        $d->setName($name);
        $d->setType("test-device");
        $d->setIp("127.0.0.10");
        $d->setPort(1234);
        $d->setCurrentState("ok");

        $binary = $d->serializeToString();

        $sock = stream_socket_client("udp://$group:$port");
        fwrite($sock, $binary);
        fclose($sock);
    }
}
