<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    private function __construct() {}

    /** @param array<string, mixed> $usuario Debe incluir 'id', 'rol_clave', 'nombre'. */
    public static function iniciar(array $usuario): void
    {
        session_regenerate_id(true);
        Session::set('usuario_id', (int) $usuario['id']);
        Session::set('usuario_rol', (string) $usuario['rol_clave']);
        Session::set('usuario_nombre', (string) $usuario['nombre']);
    }

    public static function cerrar(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] !== '' ? $params['samesite'] : 'Lax',
            ]);
        }
        session_destroy();
    }

    public static function estaAutenticado(): bool
    {
        return Session::has('usuario_id');
    }

    public static function usuarioId(): ?int
    {
        $id = Session::get('usuario_id');
        return $id === null ? null : (int) $id;
    }

    public static function rol(): ?string
    {
        $rol = Session::get('usuario_rol');
        return $rol === null ? null : (string) $rol;
    }

    public static function nombre(): ?string
    {
        $nombre = Session::get('usuario_nombre');
        return $nombre === null ? null : (string) $nombre;
    }

    public static function requiereSesion(): void
    {
        if (!self::estaAutenticado()) {
            redirect('cuenta');
        }
    }
}
