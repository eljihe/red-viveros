<?php

declare(strict_types=1);

use App\Core\Config;

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    $base = rtrim((string) Config::get('app.base_url', ''), '/');
    $normalized = '/' . ltrim($path, '/');

    if ($normalized === '/') {
        return $base === '' ? '/' : $base . '/';
    }

    return $base . $normalized;
}
