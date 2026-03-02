<?php

declare(strict_types=1);

require __DIR__ . '/src/Core/helpers.php';

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/src/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

$db = App\Core\Database::connection();
$sql = file_get_contents(__DIR__ . '/database/schema.sql');
if ($sql === false) {
    exit("Unable to read schema.sql\n");
}
$db->exec($sql);
App\Services\BootstrapService::ensureMainAdmin();

echo "Schema migrated and main admin ensured.\n";
