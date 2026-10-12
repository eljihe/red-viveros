<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\DatabaseException;
use PDOException;

final class Producto
{
    public static function crear(
        int $viveroId,
        int $categoriaId,
        int $unidadId,
        string $titulo,
        ?string $descripcion,
        ?float $precio,
        ?int $disponible,
        bool $haceEntrega,
        ?int $entregaKm,
        ?string $entregaCosto
    ): int {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('
            INSERT INTO productos (vivero_id, categoria_id, unidad_id, titulo, descripcion, precio, disponible, hace_entrega, entrega_km, entrega_costo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $viveroId, $categoriaId, $unidadId, $titulo, $descripcion, $precio, $disponible,
            $haceEntrega ? 1 : 0, $entregaKm, $entregaCosto,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function actualizar(
        int $id,
        int $viveroId,
        int $categoriaId,
        int $unidadId,
        string $titulo,
        ?string $descripcion,
        ?float $precio,
        ?int $disponible,
        bool $haceEntrega,
        ?int $entregaKm,
        ?string $entregaCosto
    ): void {
        Database::connection()->prepare('
            UPDATE productos SET
                categoria_id = ?, unidad_id = ?, titulo = ?, descripcion = ?, precio = ?,
                disponible = ?, hace_entrega = ?, entrega_km = ?, entrega_costo = ?, actualizado_en = NOW()
            WHERE id = ? AND vivero_id = ?
        ')->execute([
            $categoriaId, $unidadId, $titulo, $descripcion, $precio, $disponible,
            $haceEntrega ? 1 : 0, $entregaKm, $entregaCosto, $id, $viveroId,
        ]);
    }

    public static function eliminar(int $id, int $viveroId): void
    {
        Database::connection()->prepare('
            DELETE FROM productos WHERE id = ? AND vivero_id = ?
        ')->execute([$id, $viveroId]);
    }

    /** @return array<string, mixed>|null */
    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('
            SELECT id, vivero_id, categoria_id, unidad_id, titulo, descripcion, precio, disponible,
                   hace_entrega, entrega_km, entrega_costo, activo
            FROM productos WHERE id = ?
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /** Todos los productos propios (cualquier estado), para "Mis productos" en la cuenta. */
    public static function listByViveroId(int $viveroId): array
    {
        try {
            $stmt = Database::connection()->prepare('
                SELECT p.id, p.titulo, p.precio, p.disponible, p.hace_entrega, p.entrega_km, p.activo,
                       u.nombre AS unidad,
                       (SELECT fp.archivo FROM fotos_producto fp WHERE fp.producto_id = p.id ORDER BY fp.orden ASC, fp.id ASC LIMIT 1) AS foto_portada
                FROM productos p
                JOIN unidades u ON u.id = p.unidad_id
                WHERE p.vivero_id = ?
                ORDER BY p.creado_en DESC
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
            $stmt = Database::connection()->prepare('SELECT COUNT(*) FROM productos WHERE vivero_id = ?');
            $stmt->execute([$viveroId]);
            return (int) $stmt->fetchColumn();
        } catch (DatabaseException | PDOException) {
            return 0;
        }
    }

    /**
     * Productos públicos: solo de viveros aprobados y activos. $filtros admite
     * 'q' (texto en título), 'categoria_id', 'municipio_id', 'vivero_id', 'entrega' (bool).
     *
     * @param array<string, mixed> $filtros
     */
    public static function listPublico(array $filtros = []): array
    {
        try {
            $pdo = Database::connection();

            $condiciones = ["p.activo = 1", "v.estado = 'aprobado'"];
            $parametros = [];

            $q = trim((string) ($filtros['q'] ?? ''));
            if ($q !== '') {
                $condiciones[] = 'p.titulo LIKE ?';
                $parametros[] = '%' . $q . '%';
            }

            $categoriaId = (int) ($filtros['categoria_id'] ?? 0);
            if ($categoriaId > 0) {
                $condiciones[] = 'p.categoria_id = ?';
                $parametros[] = $categoriaId;
            }

            $municipioId = (int) ($filtros['municipio_id'] ?? 0);
            if ($municipioId > 0) {
                $condiciones[] = 'mun.id = ?';
                $parametros[] = $municipioId;
            }

            $viveroId = (int) ($filtros['vivero_id'] ?? 0);
            if ($viveroId > 0) {
                $condiciones[] = 'v.id = ?';
                $parametros[] = $viveroId;
            }

            if (!empty($filtros['entrega'])) {
                $condiciones[] = 'p.hace_entrega = 1';
            }

            $where = implode(' AND ', $condiciones);

            $stmt = $pdo->prepare("
                SELECT p.id, p.titulo, p.precio, p.disponible, p.hace_entrega, p.entrega_km, p.entrega_costo, p.creado_en,
                       c.nombre AS categoria, u.nombre AS unidad,
                       v.id AS vivero_id, v.nombre AS vivero_nombre, v.lat, v.lng,
                       com.nombre AS comunidad, mun.nombre AS municipio,
                       (SELECT fp.archivo FROM fotos_producto fp WHERE fp.producto_id = p.id ORDER BY fp.orden ASC, fp.id ASC LIMIT 1) AS foto_portada
                FROM productos p
                JOIN viveros v ON v.id = p.vivero_id
                JOIN comunidades com ON com.id = v.comunidad_id
                JOIN municipios mun ON mun.id = com.municipio_id
                JOIN categorias c ON c.id = p.categoria_id
                JOIN unidades u ON u.id = p.unidad_id
                WHERE $where
                ORDER BY p.creado_en DESC
            ");
            $stmt->execute($parametros);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (DatabaseException | PDOException) {
            return [];
        }
    }

    /** Ficha pública de un producto, incluye teléfono del responsable del vivero. */
    public static function findPublicoById(int $id): ?array
    {
        try {
            $stmt = Database::connection()->prepare("
                SELECT p.id, p.titulo, p.descripcion, p.precio, p.disponible, p.hace_entrega, p.entrega_km, p.entrega_costo, p.creado_en,
                       c.nombre AS categoria, u.nombre AS unidad, u.plural AS unidad_plural,
                       v.id AS vivero_id, v.nombre AS vivero_nombre, v.lat, v.lng,
                       com.nombre AS comunidad, mun.nombre AS municipio,
                       us.telefono AS vivero_telefono
                FROM productos p
                JOIN viveros v ON v.id = p.vivero_id
                JOIN comunidades com ON com.id = v.comunidad_id
                JOIN municipios mun ON mun.id = com.municipio_id
                JOIN categorias c ON c.id = p.categoria_id
                JOIN unidades u ON u.id = p.unidad_id
                JOIN usuarios us ON us.id = v.usuario_id
                WHERE p.id = ? AND p.activo = 1 AND v.estado = 'aprobado'
            ");
            $stmt->execute([$id]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row === false ? null : $row;
        } catch (DatabaseException | PDOException) {
            return null;
        }
    }

    /** Otros productos activos del mismo vivero, para "Más de este vivero". */
    public static function listMasDeEsteVivero(int $viveroId, int $excluirProductoId): array
    {
        try {
            $stmt = Database::connection()->prepare('
                SELECT p.id, p.titulo, p.precio, u.nombre AS unidad,
                       (SELECT fp.archivo FROM fotos_producto fp WHERE fp.producto_id = p.id ORDER BY fp.orden ASC, fp.id ASC LIMIT 1) AS foto_portada
                FROM productos p
                JOIN unidades u ON u.id = p.unidad_id
                WHERE p.vivero_id = ? AND p.id != ? AND p.activo = 1
                ORDER BY p.creado_en DESC
                LIMIT 6
            ');
            $stmt->execute([$viveroId, $excluirProductoId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (DatabaseException | PDOException) {
            return [];
        }
    }
}
