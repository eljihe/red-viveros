<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Vivero
{
    private function __construct() {}

    public static function crear(int $usuarioId, int $comunidadId, string $nombre, ?string $historia, float $lat, float $lng, ?int $integrantes): void
    {
        Database::connection()->prepare(
            'INSERT INTO viveros (usuario_id, comunidad_id, nombre, historia, lat, lng, integrantes, estado)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute([$usuarioId, $comunidadId, $nombre, $historia, $lat, $lng, $integrantes, 'pendiente']);
    }

    /** @return array<string, mixed>|null */
    public static function findByUsuarioId(int $usuarioId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, usuario_id, comunidad_id, nombre, historia, lat, lng, integrantes, estado, motivo_estado
             FROM viveros WHERE usuario_id = ?'
        );
        $stmt->execute([$usuarioId]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }
}
