<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Municipio
{
    private function __construct() {}

    /** @return array<int, array<string, mixed>> */
    public static function listAll(): array
    {
        $stmt = Database::connection()->query('SELECT id, nombre FROM municipios ORDER BY nombre');
        return $stmt->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT id, nombre FROM municipios WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }
}
