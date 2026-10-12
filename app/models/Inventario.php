<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\DatabaseException;
use PDOException;

final class Inventario
{
    public static function listByViveroId(int $viveroId): array
    {
        try {
            $pdo = Database::connection();
            $stmt = $pdo->prepare('
                SELECT i.vivero_id, i.especie_id, i.cantidad, i.edad_meses,
                       e.nombre AS especie_nombre
                FROM inventario i
                JOIN especies e ON e.id = i.especie_id
                WHERE i.vivero_id = ?
                ORDER BY e.nombre ASC
            ');
            $stmt->execute([$viveroId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (DatabaseException | PDOException) {
            return [];
        }
    }

    public static function guardar(int $viveroId, int $especieId, int $cantidad, ?int $edadMeses): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('
            INSERT INTO inventario (vivero_id, especie_id, cantidad, edad_meses)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                cantidad = VALUES(cantidad),
                edad_meses = VALUES(edad_meses),
                actualizado_en = NOW()
        ');
        $stmt->execute([$viveroId, $especieId, $cantidad, $edadMeses]);
    }

    public static function eliminar(int $viveroId, int $especieId): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('
            DELETE FROM inventario
            WHERE vivero_id = ? AND especie_id = ?
        ');
        $stmt->execute([$viveroId, $especieId]);
    }
}
