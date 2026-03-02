<?php

declare(strict_types=1);

function envv(string $key, ?string $default = null): ?string
{
    static $loaded = false;
    if (!$loaded) {
        $envPath = dirname(__DIR__, 2) . '/.env';
        if (is_file($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
                    continue;
                }
                [$k, $v] = explode('=', $line, 2);
                $k = trim($k);
                $v = trim($v);
                if ($v !== '') {
                    $first = $v[0];
                    $last = $v[strlen($v) - 1];
                    if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                        $v = substr($v, 1, -1);
                    }
                }
                $_ENV[$k] = $v;
            }
        }
        $loaded = true;
    }

    $envValue = $_ENV[$key] ?? getenv($key);

    if ($envValue === false || $envValue === null || $envValue === '') {
        return $default;
    }

    return (string) $envValue;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function csrf_token(): string
{
    if (!isset($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(16));
    }

    return $_SESSION['_csrf'];
}

function verify_csrf(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return;
    }

    $token = $_POST['_csrf'] ?? '';
    if (!$token || !hash_equals($_SESSION['_csrf'] ?? '', (string)$token)) {
        http_response_code(419);
        exit('Invalid CSRF token');
    }
}
