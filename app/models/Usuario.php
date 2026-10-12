<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use DateTimeImmutable;

final class Usuario
{
    private function __construct() {}

    /** @return array<string, mixed>|null */
    public static function findByTelefono(string $telefono): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT u.*, r.clave AS rol_clave, r.nombre AS rol_nombre
             FROM usuarios u INNER JOIN roles r ON r.id = u.rol_id
             WHERE u.telefono = ?'
        );
        $stmt->execute([$telefono]);
        $usuario = $stmt->fetch();
        return $usuario === false ? null : $usuario;
    }

    /** @return array<string, mixed>|null */
    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT u.*, r.clave AS rol_clave, r.nombre AS rol_nombre
             FROM usuarios u INNER JOIN roles r ON r.id = u.rol_id
             WHERE u.id = ?'
        );
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();
        return $usuario === false ? null : $usuario;
    }

    /** @return array<string, mixed>|null */
    public static function findPrimerAdmin(): ?array
    {
        $stmt = Database::connection()->query(
            "SELECT u.* FROM usuarios u INNER JOIN roles r ON r.id = u.rol_id
             WHERE r.clave = 'admin' AND u.estado = 'activo' ORDER BY u.id ASC LIMIT 1"
        );
        $admin = $stmt->fetch();
        return $admin === false ? null : $admin;
    }

    public static function registrarIntentoFallido(int $id): void
    {
        $pdo = Database::connection();
        $pdo->prepare('UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1 WHERE id = ?')->execute([$id]);

        $consultar = $pdo->prepare('SELECT intentos_fallidos FROM usuarios WHERE id = ?');
        $consultar->execute([$id]);
        $intentos = (int) $consultar->fetchColumn();

        if ($intentos >= 5) {
            $bloqueadoHasta = (new DateTimeImmutable('now'))->modify('+15 minutes')->format('Y-m-d H:i:s');
            $pdo->prepare('UPDATE usuarios SET bloqueado_hasta = ? WHERE id = ?')->execute([$bloqueadoHasta, $id]);
        }
    }

    public static function registrarIntentoExitoso(int $id): void
    {
        Database::connection()
            ->prepare('UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL, ultimo_acceso = NOW() WHERE id = ?')
            ->execute([$id]);
    }

    public static function resetearBloqueo(int $id): void
    {
        Database::connection()
            ->prepare('UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = ?')
            ->execute([$id]);
    }

    public static function actualizarPin(int $id, string $pinHash): void
    {
        Database::connection()
            ->prepare('UPDATE usuarios SET pin_hash = ?, debe_cambiar_pin = 0 WHERE id = ?')
            ->execute([$pinHash, $id]);
    }

    public static function actualizarPinHash(int $id, string $pinHash): void
    {
        Database::connection()->prepare('UPDATE usuarios SET pin_hash = ? WHERE id = ?')->execute([$pinHash, $id]);
    }
}
