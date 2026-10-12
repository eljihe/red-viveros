<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\DatabaseException;
use PDOException;

final class FotoProducto
{
    public static function listByProductoId(int $productoId): array
    {
        try {
            $pdo = Database::connection();
            $stmt = $pdo->prepare('
                SELECT id, producto_id, archivo, orden, creado_en
                FROM fotos_producto
                WHERE producto_id = ?
                ORDER BY orden ASC, id ASC
            ');
            $stmt->execute([$productoId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (DatabaseException | PDOException) {
            return [];
        }
    }

    public static function crear(int $productoId, string $archivo, int $orden): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('
            INSERT INTO fotos_producto (producto_id, archivo, orden)
            VALUES (?, ?, ?)
        ');
        $stmt->execute([$productoId, $archivo, $orden]);
        return (int) $pdo->lastInsertId();
    }

    public static function eliminar(int $id, int $productoId): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('
            DELETE FROM fotos_producto
            WHERE id = ? AND producto_id = ?
        ');
        $stmt->execute([$id, $productoId]);
    }

    public static function contarPorProducto(int $productoId): int
    {
        try {
            $pdo = Database::connection();
            $stmt = $pdo->prepare('
                SELECT COUNT(*) as total FROM fotos_producto WHERE producto_id = ?
            ');
            $stmt->execute([$productoId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) ($result['total'] ?? 0);
        } catch (DatabaseException | PDOException) {
            return 0;
        }
    }
}
