<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\DatabaseException;
use PDOException;

final class Servicio
{
    public static function crear(int $viveroId, string $titulo, ?string $descripcion, ?string $horario, ?string $costo): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('
            INSERT INTO servicios (vivero_id, titulo, descripcion, horario, costo)
            VALUES (?, ?, ?, ?, ?)
        ');
        $stmt->execute([$viveroId, $titulo, $descripcion, $horario, $costo]);
        return (int) $pdo->lastInsertId();
    }

    public static function eliminar(int $id, int $viveroId): void
    {
        Database::connection()->prepare('
            DELETE FROM servicios WHERE id = ? AND vivero_id = ?
        ')->execute([$id, $viveroId]);
    }

    /** @return array<string, mixed>|null */
    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('
            SELECT id, vivero_id, titulo, descripcion, horario, costo, activo
            FROM servicios WHERE id = ?
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /** Todos los servicios propios (cualquier estado), para el panel del vivero. */
    public static function listByViveroId(int $viveroId): array
    {
        try {
            $stmt = Database::connection()->prepare('
                SELECT id, titulo, descripcion, horario, costo, activo
                FROM servicios
                WHERE vivero_id = ?
                ORDER BY creado_en ASC
            ');
            $stmt->execute([$viveroId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (DatabaseException | PDOException) {
            return [];
        }
    }

    public static function countByViveroId(int $viveroId): int
    {
        try {
            $stmt = Database::connection()->prepare('SELECT COUNT(*) FROM servicios WHERE vivero_id = ?');
            $stmt->execute([$viveroId]);
            return (int) $stmt->fetchColumn();
        } catch (DatabaseException | PDOException) {
            return 0;
        }
    }

    /** Servicios activos de varios viveros a la vez, agrupados por vivero_id. Para el mapa público. */
    public static function listActivosPorViveros(array $viveroIds): array
    {
        if ($viveroIds === []) {
            return [];
        }

        try {
            $placeholders = implode(',', array_fill(0, count($viveroIds), '?'));
            $stmt = Database::connection()->prepare("
                SELECT vivero_id, id, titulo, descripcion, horario, costo
                FROM servicios
                WHERE vivero_id IN ($placeholders) AND activo = 1
                ORDER BY creado_en ASC
            ");
            $stmt->execute($viveroIds);

            $porVivero = [];
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $fila) {
                $porVivero[(int) $fila['vivero_id']][] = $fila;
            }

            return $porVivero;
        } catch (DatabaseException | PDOException) {
            return [];
        }
    }
}
