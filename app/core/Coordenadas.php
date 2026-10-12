<?php

declare(strict_types=1);

namespace App\Core;

final class Coordenadas
{
    /**
     * Acepta decimales ("18.2645, -93.2213") o grados/minutos/segundos
     * ("18°12'04.3"N 93°13'28.2"W"), como las que da Google Maps o un GPS.
     *
     * @return array{0: float|null, 1: float|null}
     */
    public static function parse(string $text): array
    {
        $text = trim($text);

        $dms = self::parseDms($text);
        if ($dms !== null) {
            return $dms;
        }

        $parts = explode(',', $text);
        if (count($parts) !== 2) {
            return [null, null];
        }

        $lat = self::aFloat(trim($parts[0]));
        $lng = self::aFloat(trim($parts[1]));

        if ($lat === null || $lng === null) {
            return [null, null];
        }

        return [$lat, $lng];
    }

    public static function esValidaTabasco(float $lat, float $lng): bool
    {
        return $lat >= 14 && $lat <= 33 && $lng >= -118 && $lng <= -86;
    }

    /** @return array{0: float, 1: float}|null */
    private static function parseDms(string $text): ?array
    {
        $patron = '/(\d{1,3})\s*[°º]\s*(\d{1,2})\s*[\'′]\s*(\d{1,2}(?:\.\d+)?)\s*["″]?\s*([NSEWnsew])/u';

        if (preg_match_all($patron, $text, $coincidencias, PREG_SET_ORDER) !== 2) {
            return null;
        }

        $valores = [];
        foreach ($coincidencias as $coincidencia) {
            $grados = (float) $coincidencia[1];
            $minutos = (float) $coincidencia[2];
            $segundos = (float) $coincidencia[3];
            $direccion = strtoupper($coincidencia[4]);

            $decimal = $grados + ($minutos / 60) + ($segundos / 3600);
            if ($direccion === 'S' || $direccion === 'W') {
                $decimal *= -1;
            }

            $valores[$direccion] = $decimal;
        }

        $lat = $valores['N'] ?? $valores['S'] ?? null;
        $lng = $valores['E'] ?? $valores['W'] ?? null;

        if ($lat === null || $lng === null) {
            return null;
        }

        return [$lat, $lng];
    }

    private static function aFloat(string $valor): ?float
    {
        if ($valor === '' || !is_numeric($valor)) {
            return null;
        }

        return (float) $valor;
    }
}
