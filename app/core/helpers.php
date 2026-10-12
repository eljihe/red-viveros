<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Csrf;
use App\Core\Session;

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

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function csrfField(): string
{
    return Csrf::field();
}

function flashSet(string $tipo, string $mensaje): void
{
    Session::set('cuenta_flash', ['tipo' => $tipo, 'mensaje' => $mensaje]);
}

/** @return array{tipo: string|null, mensaje: string|null} */
function flashGet(): array
{
    $flash = Session::get('cuenta_flash');
    Session::remove('cuenta_flash');
    if (!is_array($flash) || !isset($flash['tipo'], $flash['mensaje'])) {
        return ['tipo' => null, 'mensaje' => null];
    }
    return $flash;
}
