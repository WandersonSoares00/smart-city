<?php

namespace Gateway;

use PDO;

class Database
{
    private static ?PDO $conn = null;

    public static function getConnection(): PDO
    {
        $host = $_ENV['MYSQL_HOST'] ?? 'mysql';
        $port = $_ENV['MYSQL_PORT'] ?? 3306;
        $db   = $_ENV['MYSQL_DATABASE'];
        $user = $_ENV['MYSQL_USER'];
        $pass = $_ENV['MYSQL_PASSWORD'];

        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

        $attempts = 0;
        $maxAttempts = 15;

        if (self::$conn !== null) {
            return self::$conn;
        }
        
        while ($attempts < $maxAttempts) {
            try {
                self::$conn = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                return self::$conn;
            } catch (\PDOException $e) {
                $attempts++;
                echo "[DB] Aguardando MySQL ($attempts/$maxAttempts)...\n";
                sleep(2);
            }
        }

        throw new \RuntimeException("MySQL não ficou disponível a tempo");
    }

    public static function closeConnection(): void
    {
        self::$conn = null;
    }
}