<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\DatabaseException;
use PDOException;

final class FotoVivero
{
    public static function listByViveroId(int $viveroId): array
    {
        try {
            $pdo = Database::connection();
            $stmt = $pdo->prepare('
                SELECT id, vivero_id, archivo, orden, creado_en
                FROM fotos_vivero
                WHERE vivero_id = ?
                ORDER BY orden ASC, id ASC
            ');
            $stmt->execute([$viveroId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (DatabaseException | PDOException) {
            return [];
        }
    }

    public static function crear(int $viveroId, string $archivo, int $orden): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('
            INSERT INTO fotos_vivero (vivero_id, archivo, orden)
            VALUES (?, ?, ?)
        ');
        $stmt->execute([$viveroId, $archivo, $orden]);
        return (int) $pdo->lastInsertId();
    }

    public static function eliminar(int $id, int $viveroId): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('
            DELETE FROM fotos_vivero
            WHERE id = ? AND vivero_id = ?
        ');
        $stmt->execute([$id, $viveroId]);
    }

    public static function contarPorVivero(int $viveroId): int
    {
        try {
            $pdo = Database::connection();
            $stmt = $pdo->prepare('
                SELECT COUNT(*) as total FROM fotos_vivero WHERE vivero_id = ?
            ');
            $stmt->execute([$viveroId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) ($result['total'] ?? 0);
        } catch (DatabaseException | PDOException) {
            return 0;
        }
    }
}
