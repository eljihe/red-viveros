<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    private const SESSION_KEY = 'csrf_token';

    private function __construct()
    {
    }

    public static function token(): string
    {
        if (!Session::has(self::SESSION_KEY)) {
            Session::set(self::SESSION_KEY, bin2hex(random_bytes(32)));
        }

        return (string) Session::get(self::SESSION_KEY);
    }

    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(self::token()) . '">';
    }

    public static function isValid(?string $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }

        $stored = Session::get(self::SESSION_KEY);

        return is_string($stored) && hash_equals($stored, $token);
    }
}
