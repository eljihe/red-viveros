<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\DatabaseException;
use App\Core\View;
use App\Models\Usuario;
use PDOException;

final class CuentaController
{
    public function mostrar(): void
    {
        $flash = flashGet();
        $mensaje = $flash['mensaje'];
        $tipo = $flash['tipo'];

        if ($mensaje === null && ($_GET['salida'] ?? '') === '1') {
            $mensaje = 'Sesión cerrada correctamente.';
            $tipo = 'ok';
        }

        if (!Auth::estaAutenticado()) {
            echo View::render('cuenta/login', [
                'mensaje' => $mensaje,
                'tipo' => $tipo,
                'activo' => 'cuenta',
            ]);
            return;
        }

        try {
            $usuario = Usuario::findById((int) Auth::usuarioId());
        } catch (DatabaseException | PDOException $exception) {
            $usuario = null;
        }

        if ($usuario === null || $usuario['estado'] === 'bloqueado') {
            Auth::cerrar();
            redirect('cuenta');
        }

        if ((int) $usuario['debe_cambiar_pin'] === 1) {
            redirect('cuenta/cambiar-pin');
        }

        echo View::render('cuenta/panel', [
            'usuario' => $usuario,
            'mensaje' => $mensaje,
            'tipo' => $tipo,
            'activo' => 'cuenta',
        ]);
    }

    public function mostrarCambioPin(): void
    {
        Auth::requiereSesion();
        $flash = flashGet();
        echo View::render('cuenta/cambiar-pin', [
            'mensaje' => $flash['mensaje'],
            'tipo' => $flash['tipo'],
            'activo' => 'cuenta',
        ]);
    }

    public function cambiarPin(): void
    {
        Auth::requiereSesion();

        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            flashSet('err', 'La página expiró, inténtalo de nuevo.');
            redirect('cuenta/cambiar-pin');
        }

        $pinNuevo = (string) ($_POST['pin_nuevo'] ?? '');
        $pinConfirmacion = (string) ($_POST['pin_confirmacion'] ?? '');

        if (strlen($pinNuevo) !== 6 || !ctype_digit($pinNuevo)) {
            flashSet('err', 'El PIN nuevo debe tener 6 dígitos.');
            redirect('cuenta/cambiar-pin');
        }

        if ($pinNuevo !== $pinConfirmacion) {
            flashSet('err', 'La confirmación no coincide con el PIN nuevo.');
            redirect('cuenta/cambiar-pin');
        }

        try {
            $hash = password_hash($pinNuevo, PASSWORD_BCRYPT, ['cost' => 12]);
            Usuario::actualizarPin((int) Auth::usuarioId(), $hash);
        } catch (DatabaseException | PDOException $exception) {
            flashSet('err', 'No se pudo guardar tu PIN, inténtalo más tarde.');
            redirect('cuenta/cambiar-pin');
        }

        flashSet('ok', 'Tu PIN se actualizó correctamente.');
        redirect('cuenta');
    }
}
