<?php

namespace Gateway;

class MulticastDiscovery
{
    public static function send(string $group, int $port, string $message): void
    {
        $sock = @stream_socket_client("udp://$group:$port", $errno, $errstr);

        if ($sock) {
            fwrite($sock, $message);
            fclose($sock);
        }
    }
}
