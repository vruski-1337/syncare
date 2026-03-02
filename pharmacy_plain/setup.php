<?php

declare(strict_types=1);

require __DIR__ . '/src/Core/helpers.php';

function adaptSchemaForSqlite(string $schema): string
{
    $converted = preg_replace('/\bINT\s+AUTO_INCREMENT\s+PRIMARY\s+KEY\b/i', 'INTEGER PRIMARY KEY', $schema) ?? $schema;
    $converted = preg_replace('/\bAUTO_INCREMENT\b/i', '', $converted) ?? $converted;
    $converted = preg_replace('/\bTINYINT\s*\(\s*1\s*\)/i', 'INTEGER', $converted) ?? $converted;
    $converted = preg_replace('/\bENUM\s*\([^\)]*\)/i', 'TEXT', $converted) ?? $converted;
    $converted = preg_replace('/\bJSON\b/i', 'TEXT', $converted) ?? $converted;
    $converted = preg_replace('/\bDECIMAL\s*\(\s*\d+\s*,\s*\d+\s*\)/i', 'NUMERIC', $converted) ?? $converted;

    return $converted;
}

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
$driver = (string) $db->getAttribute(PDO::ATTR_DRIVER_NAME);
if ($driver === 'sqlite') {
    $sql = adaptSchemaForSqlite($sql);
}
$db->exec($sql);
App\Services\BootstrapService::ensureMainAdmin();

echo "Schema migrated and main admin ensured.\n";
