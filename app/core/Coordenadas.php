<?php

declare(strict_types=1);

namespace App\Core;

final class Coordenadas
{
    public static function parse(string $text): array
    {
        $text = trim($text);
        $parts = explode(',', $text);
        if (count($parts) !== 2) {
            return [null, null];
        }

        $lat = (float) trim($parts[0]);
        $lng = (float) trim($parts[1]);

        return [$lat, $lng];
    }

    public static function esValidaTabasco(float $lat, float $lng): bool
    {
        return $lat >= 14 && $lat <= 33 && $lng >= -118 && $lng <= -86;
    }
}
