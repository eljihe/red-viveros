<?php

declare(strict_types=1);

namespace App\Core;

final class Config
{
    /** @var array<string, mixed> */
    private static array $data = [];

    private function __construct()
    {
    }

    public static function load(string $path): void
    {
        if (!is_file($path)) {
            throw new AppException("No se encontró el archivo de configuración: {$path}");
        }

        /** @var array<string, mixed> $data */
        $data = require $path;
        self::$data = $data;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = self::$data;

        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }
}
