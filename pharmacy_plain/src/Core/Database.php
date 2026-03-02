<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $pdo = null;

    private static function cfg(array $keys, ?string $default = null): ?string
    {
        foreach ($keys as $key) {
            $value = envv($key);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return $default;
    }

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $driver = strtolower((string) self::cfg(['DB_CONNECTION'], 'mysql'));

        if ($driver === 'sqlite') {
            $database = self::cfg(['DB_DATABASE', 'DB_NAME'], dirname(__DIR__, 2) . '/database/pharmacy_plain.sqlite');
            if ($database === ':memory:') {
                $path = ':memory:';
            } else {
                $path = str_starts_with($database, '/') ? $database : dirname(__DIR__, 2) . '/' . ltrim($database, '/');
                $dir = dirname($path);
                if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
                    throw new RuntimeException('Unable to create SQLite directory: ' . $dir);
                }
                if (!is_file($path) && !touch($path)) {
                    throw new RuntimeException('Unable to create SQLite database file: ' . $path);
                }
            }

            self::$pdo = new PDO('sqlite:' . $path, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            self::$pdo->exec('PRAGMA foreign_keys = ON');

            return self::$pdo;
        }

        $host = self::cfg(['DB_HOST'], 'localhost');
        $port = self::cfg(['DB_PORT'], '3306');
        $name = self::cfg(['DB_NAME', 'DB_DATABASE'], 'pharmacy_plain');
        $user = self::cfg(['DB_USER', 'DB_USERNAME'], 'root');
        $pass = self::cfg(['DB_PASS', 'DB_PASSWORD'], '');

        $hosts = array_values(array_unique([$host, 'localhost', '127.0.0.1']));
        $lastException = null;

        foreach ($hosts as $candidateHost) {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $candidateHost, $port, $name);

            try {
                self::$pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);

                return self::$pdo;
            } catch (PDOException $e) {
                $lastException = $e;
            }
        }

        $reason = $lastException?->getMessage() ?? 'Unknown connection error.';
        throw new RuntimeException(
            'Database connection failed. Check pharmacy_plain/.env settings for the selected DB_CONNECTION and ensure the database service/file is available. Reason: ' . $reason,
            previous: $lastException
        );

    }
}
