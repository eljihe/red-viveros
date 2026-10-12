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

    public static function actualizar(int $id, string $nombre, ?string $historia, float $lat, float $lng, ?int $integrantes): void
    {
        Database::connection()->prepare(
            'UPDATE viveros SET nombre = ?, historia = ?, lat = ?, lng = ?, integrantes = ?, actualizado_en = NOW()
             WHERE id = ?'
        )->execute([$nombre, $historia, $lat, $lng, $integrantes, $id]);
    }

    /**
     * Viveros aprobados con su comunidad/municipio, inventario y fotos, para el mapa público.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function listAprobadosConDetalle(): array
    {
        $pdo = Database::connection();

        $stmt = $pdo->query(
            "SELECT v.id, v.nombre, v.historia, v.lat, v.lng, v.integrantes,
                    c.nombre AS comunidad, m.id AS municipio_id, m.nombre AS municipio
             FROM viveros v
             JOIN comunidades c ON c.id = v.comunidad_id
             JOIN municipios m ON m.id = c.municipio_id
             WHERE v.estado = 'aprobado'
             ORDER BY v.nombre ASC"
        );
        $viveros = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        if ($viveros === []) {
            return [];
        }

        $ids = array_map(static fn (array $v): int => (int) $v['id'], $viveros);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmtInventario = $pdo->prepare(
            "SELECT i.vivero_id, i.especie_id, i.cantidad, i.edad_meses, e.nombre AS especie_nombre
             FROM inventario i
             JOIN especies e ON e.id = i.especie_id
             WHERE i.vivero_id IN ($placeholders)
             ORDER BY e.nombre ASC"
        );
        $stmtInventario->execute($ids);
        $inventarioPorVivero = [];
        foreach ($stmtInventario->fetchAll(\PDO::FETCH_ASSOC) as $fila) {
            $inventarioPorVivero[(int) $fila['vivero_id']][] = $fila;
        }

        $stmtFotos = $pdo->prepare(
            "SELECT vivero_id, archivo FROM fotos_vivero WHERE vivero_id IN ($placeholders) ORDER BY orden ASC, id ASC"
        );
        $stmtFotos->execute($ids);
        $fotosPorVivero = [];
        foreach ($stmtFotos->fetchAll(\PDO::FETCH_ASSOC) as $fila) {
            $fotosPorVivero[(int) $fila['vivero_id']][] = $fila['archivo'];
        }

        foreach ($viveros as &$vivero) {
            $id = (int) $vivero['id'];
            $vivero['inventario'] = $inventarioPorVivero[$id] ?? [];
            $vivero['fotos'] = $fotosPorVivero[$id] ?? [];
        }

        return $viveros;
    }
}
