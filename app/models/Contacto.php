<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\DatabaseException;
use PDOException;

final class Contacto
{
    private const LIMITE_NORMAL = 15;
    private const LIMITE_CUENTA_NUEVA = 3;
    private const HORAS_CUENTA_NUEVA = 24;

    public static function yaContactoHoy(int $usuarioId, int $productoId): bool
    {
        try {
            $stmt = Database::connection()->prepare('
                SELECT COUNT(*) FROM contactos
                WHERE usuario_id = ? AND producto_id = ? AND creado_en >= CURDATE()
            ');
            $stmt->execute([$usuarioId, $productoId]);
            return ((int) $stmt->fetchColumn()) > 0;
        } catch (DatabaseException | PDOException) {
            return false;
        }
    }

    /**
     * Límite diario de contactos: 15 normalmente, 3 en las primeras 24h de la cuenta.
     *
     * @return array{puede: bool, razon: string|null}
     */
    public static function puedeContactar(int $usuarioId): array
    {
        try {
            $pdo = Database::connection();

            $stmt = $pdo->prepare('SELECT creado_en FROM usuarios WHERE id = ?');
            $stmt->execute([$usuarioId]);
            $creadoEn = $stmt->fetchColumn();

            $cuentaNueva = $creadoEn !== false
                && strtotime((string) $creadoEn) >= (time() - self::HORAS_CUENTA_NUEVA * 3600);

            $limite = $cuentaNueva ? self::LIMITE_CUENTA_NUEVA : self::LIMITE_NORMAL;

            $stmtContactos = $pdo->prepare('
                SELECT COUNT(*) FROM contactos WHERE usuario_id = ? AND creado_en >= CURDATE()
            ');
            $stmtContactos->execute([$usuarioId]);
            $contactosHoy = (int) $stmtContactos->fetchColumn();

            if ($contactosHoy >= $limite) {
                return ['puede' => false, 'razon' => 'Alcanzaste tu límite de contactos por hoy. Intenta de nuevo mañana.'];
            }

            return ['puede' => true, 'razon' => null];
        } catch (DatabaseException | PDOException) {
            return ['puede' => false, 'razon' => 'No se pudo verificar tu límite de contactos. Intenta más tarde.'];
        }
    }

    public static function crear(int $usuarioId, int $productoId): void
    {
        Database::connection()->prepare('
            INSERT INTO contactos (usuario_id, producto_id) VALUES (?, ?)
        ')->execute([$usuarioId, $productoId]);
    }
}
