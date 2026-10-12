<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $parts = explode('\\', $relative);

    // El primer segmento del namespace (Core, Controllers, Models)
    // corresponde a una carpeta en minúsculas: app/core, app/controllers, app/models.
    $parts[0] = strtolower($parts[0]);

    $file = dirname(__DIR__) . '/' . implode('/', $parts) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

require __DIR__ . '/helpers.php';
