<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Comunidad
{
    private function __construct() {}

    /** @return array<int, array<string, mixed>> */
    public static function listAll(): array
    {
        $stmt = Database::connection()->query(
            'SELECT id, municipio_id, nombre FROM comunidades WHERE activo = 1 ORDER BY municipio_id, nombre'
        );
        return $stmt->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, municipio_id, nombre FROM comunidades WHERE id = ? AND activo = 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }
}
