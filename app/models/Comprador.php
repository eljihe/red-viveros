<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Comprador
{
    private function __construct() {}

    public static function crear(int $usuarioId, int $municipioId, string $comunidadColonia, string $tipoComprador, ?string $rfc, ?float $lat, ?float $lng): void
    {
        Database::connection()->prepare(
            'INSERT INTO compradores (usuario_id, municipio_id, comunidad_colonia, tipo_comprador, rfc, lat, lng)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        )->execute([$usuarioId, $municipioId, $comunidadColonia, $tipoComprador, $rfc, $lat, $lng]);
    }

    /** @return array<string, mixed>|null */
    public static function findByUsuarioId(int $usuarioId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT usuario_id, municipio_id, comunidad_colonia, tipo_comprador, rfc, lat, lng FROM compradores WHERE usuario_id = ?'
        );
        $stmt->execute([$usuarioId]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }
}
