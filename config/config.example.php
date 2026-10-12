<?php

declare(strict_types=1);

// Copia este archivo como config.php y ajusta los valores según el entorno.
// config.php ya está en .gitignore y nunca debe subirse al repositorio.

return [
    'app' => [
        // 'local' en XAMPP, 'production' en GoDaddy.
        'env' => 'local',
        // Mostrar detalle de errores (solo true en local).
        'debug' => true,
        'name' => 'Red de Viveros Tabasco',
        // Ruta base de la app, SIN slash final.
        // Local (XAMPP, subcarpeta):   '/red-viveros'
        // Producción (GoDaddy, raíz):  ''
        'base_url' => '/red-viveros',
        // Zona horaria (https://www.php.net/manual/en/timezones.php)
        'timezone' => 'America/Mexico_City',
    ],
    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'red_viveros',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
];
