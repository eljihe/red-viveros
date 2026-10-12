<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\DatabaseException;
use App\Core\View;
use App\Models\Usuario;
use PDOException;

final class AuthController
{
    private const PIN_HASH_FICTICIO = '$2y$12$zJDleQvJyEtgEck/3SzKQ.oViiywps9yfts4JHRGI3O7JqD52VJBu';

    public function entrar(): void
    {
        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            flashSet('err', 'La página expiró, inténtalo de nuevo.');
            redirect('cuenta');
        }

        $telefono = trim((string) ($_POST['telefono'] ?? ''));
        $pin = (string) ($_POST['pin'] ?? '');

        if (strlen($telefono) !== 10 || !ctype_digit($telefono)) {
            flashSet('err', 'Escribe tu teléfono a 10 dígitos.');
            redirect('cuenta');
        }

        if (strlen($pin) !== 6 || !ctype_digit($pin)) {
            flashSet('err', 'El PIN debe tener 6 dígitos.');
            redirect('cuenta');
        }

        try {
            $usuario = Usuario::findByTelefono($telefono);

            if ($usuario === null) {
                password_verify($pin, self::PIN_HASH_FICTICIO);
                flashSet('err', 'Teléfono o PIN incorrectos.');
                redirect('cuenta');
            }

            if ($usuario['estado'] === 'bloqueado') {
                flashSet('err', 'Tu cuenta está bloqueada. Escribe al administrador por WhatsApp.');
                redirect('cuenta');
            }

            $bloqueadoHasta = $usuario['bloqueado_hasta'];

            if ($bloqueadoHasta !== null) {
                if (strtotime((string) $bloqueadoHasta) > time()) {
                    flashSet('err', 'Demasiados intentos. Espera unos minutos e inténtalo de nuevo.');
                    redirect('cuenta');
                }
                Usuario::resetearBloqueo((int) $usuario['id']);
            }

            if (!password_verify($pin, $usuario['pin_hash'])) {
                Usuario::registrarIntentoFallido((int) $usuario['id']);
                flashSet('err', 'Teléfono o PIN incorrectos.');
                redirect('cuenta');
            }

            Usuario::registrarIntentoExitoso((int) $usuario['id']);

            if (password_needs_rehash($usuario['pin_hash'], PASSWORD_BCRYPT, ['cost' => 12])) {
                Usuario::actualizarPinHash((int) $usuario['id'], password_hash($pin, PASSWORD_BCRYPT, ['cost' => 12]));
            }

            Auth::iniciar($usuario);
            redirect((int) $usuario['debe_cambiar_pin'] === 1 ? 'cuenta/cambiar-pin' : 'cuenta');
        } catch (DatabaseException | PDOException $exception) {
            flashSet('err', 'No se pudo procesar tu inicio de sesión, inténtalo más tarde.');
            redirect('cuenta');
        }
    }

    public function salir(): void
    {
        if (Csrf::isValid($_POST['csrf_token'] ?? null)) {
            Auth::cerrar();
        }
        redirect('cuenta?salida=1');
    }

    public function olvidePin(): void
    {
        try {
            $admin = Usuario::findPrimerAdmin();
        } catch (DatabaseException | PDOException $exception) {
            $admin = null;
        }

        $enlaceWhatsApp = null;
        if ($admin !== null) {
            $texto = rawurlencode('Hola, olvidé mi PIN de Red de Viveros. Mi teléfono registrado es: ');
            $enlaceWhatsApp = 'https://wa.me/52' . $admin['telefono'] . '?text=' . $texto;
        }

        echo View::render('cuenta/olvide-pin', [
            'enlaceWhatsApp' => $enlaceWhatsApp,
            'activo' => 'cuenta',
        ]);
    }
}
