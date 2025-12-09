<?php

namespace Gateway;

class BroadcastDiscovery
{
    public static function send(string $broadcastAddr, int $port, string $message): bool
    {
        // Cria socket UDP
        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

        if ($sock === false) {
            echo "[ERROR] Falha ao criar socket: " . socket_strerror(socket_last_error()) . "\n";
            return false;
        }

        // Habilita broadcast
        if (!socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1)) {
            echo "[ERROR] Falha ao habilitar broadcast\n";
            return false;
        }

        $sent = socket_sendto($sock, $message, strlen($message), 0, $broadcastAddr, $port);

        if ($sent === false) {
            echo "[ERROR] Falha ao enviar: " . socket_strerror(socket_last_error($sock)) . "\n";
            return false;
        }

        socket_close($sock);
        return true;
    }
}
