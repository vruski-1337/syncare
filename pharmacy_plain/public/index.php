<?php

declare(strict_types=1);

session_start();

require dirname(__DIR__) . '/src/Core/helpers.php';

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = dirname(__DIR__) . '/src/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

try {
    App\Services\BootstrapService::ensureMainAdmin();
    App\Core\App::run();
} catch (\Throwable $e) {
    http_response_code(503);
    echo '<h1>Database connection error</h1>';
    echo '<p>' . e($e->getMessage()) . '</p>';
    if (envv('APP_DEBUG', 'false') === 'true') {
        echo '<pre>' . e($e->getTraceAsString()) . '</pre>';
    }
}
