<?php

declare(strict_types=1);

$root = dirname(__DIR__);

return [
    'root' => $root,
    'app' => $root . '/app',
    'public' => $root . '/public',
    'config' => $root . '/config',
    'logs' => $root . '/logs',
    'uploads' => [
        'viveros' => $root . '/public/uploads/viveros',
        'productos' => $root . '/public/uploads/productos',
    ],
];
