<?php

declare(strict_types=1);

namespace App\Core;

final class Logger
{
    private static ?string $logsDir = null;

    public static function init(string $logsDir): void
    {
        self::$logsDir = $logsDir;
        if (!is_dir($logsDir)) {
            mkdir($logsDir, 0755, true);
        }
    }

    public static function error(string $mensaje, ?array $contexto = null): void
    {
        self::log('error', $mensaje, $contexto);
    }

    public static function info(string $mensaje, ?array $contexto = null): void
    {
        self::log('info', $mensaje, $contexto);
    }

    public static function audit(string $accion, string $usuario, string $resultado, ?array $detalles = null): void
    {
        $contexto = ['usuario' => $usuario, 'resultado' => $resultado];
        if ($detalles !== null) {
            $contexto['detalles'] = $detalles;
        }
        self::log('audit', $accion, $contexto);
    }

    private static function log(string $nivel, string $mensaje, ?array $contexto = null): void
    {
        if (self::$logsDir === null) {
            return;
        }

        $archivo = self::$logsDir . '/' . $nivel . '.log';
        $timestamp = date('Y-m-d H:i:s');

        $linea = "[$timestamp] $mensaje";
        if ($contexto !== null) {
            $linea .= " | " . json_encode($contexto, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        $linea .= "\n";

        error_log($linea, 3, $archivo);
    }
}
