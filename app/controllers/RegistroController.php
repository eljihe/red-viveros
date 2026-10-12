<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\DatabaseException;
use App\Core\View;
use App\Models\Comprador;
use App\Models\Comunidad;
use App\Models\Municipio;
use App\Models\Usuario;
use App\Models\Vivero;
use PDOException;

final class RegistroController
{
    public function elegirTipo(): void
    {
        if (Auth::estaAutenticado()) {
            redirect('cuenta');
        }
        echo View::render('registro/elegir-tipo', ['activo' => 'registro']);
    }

    public function mostrarComprador(): void
    {
        if (Auth::estaAutenticado()) {
            redirect('cuenta');
        }
        try {
            $municipios = Municipio::listAll();
        } catch (DatabaseException | PDOException $exception) {
            $municipios = [];
        }
        echo View::render('registro/comprador', [
            'municipios' => $municipios,
            'activo' => 'registro',
        ]);
    }

    public function crearComprador(): void
    {
        if (Auth::estaAutenticado()) {
            redirect('registro');
        }

        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            flashSet('err', 'La página expiró, inténtalo de nuevo.');
            redirect('registro/comprador');
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $apellidos = trim((string) ($_POST['apellidos'] ?? ''));
        $telefono = trim((string) ($_POST['telefono'] ?? ''));
        $pin = (string) ($_POST['pin'] ?? '');
        $pinConfirmacion = (string) ($_POST['pin_confirmacion'] ?? '');
        $email = trim((string) ($_POST['email'] ?? '')) ?: null;
        $municipioId = (int) ($_POST['municipio_id'] ?? 0);
        $comunidadColonia = trim((string) ($_POST['comunidad_colonia'] ?? ''));
        $tipoComprador = (string) ($_POST['tipo_comprador'] ?? 'particular');
        $rfc = trim((string) ($_POST['rfc'] ?? '')) ?: null;
        $coordenadas = trim((string) ($_POST['coordenadas'] ?? '')) ?: null;
        $aceptoTerminos = (bool) ($_POST['acepto_terminos'] ?? false);

        if (!$aceptoTerminos) {
            flashSet('err', 'Debes aceptar los términos de uso.');
            redirect('registro/comprador');
        }

        if (strlen($nombre) === 0 || strlen($nombre) > 60) {
            flashSet('err', 'El nombre debe tener entre 1 y 60 caracteres.');
            redirect('registro/comprador');
        }

        if (strlen($apellidos) === 0 || strlen($apellidos) > 80) {
            flashSet('err', 'Los apellidos deben tener entre 1 y 80 caracteres.');
            redirect('registro/comprador');
        }

        if (strlen($telefono) !== 10 || !ctype_digit($telefono)) {
            flashSet('err', 'El teléfono debe tener 10 dígitos.');
            redirect('registro/comprador');
        }

        if (strlen($pin) !== 6 || !ctype_digit($pin)) {
            flashSet('err', 'El PIN debe tener 6 dígitos.');
            redirect('registro/comprador');
        }

        if ($pin !== $pinConfirmacion) {
            flashSet('err', 'La confirmación del PIN no coincide.');
            redirect('registro/comprador');
        }

        if ($email !== null && strlen($email) > 120) {
            flashSet('err', 'El email debe tener máximo 120 caracteres.');
            redirect('registro/comprador');
        }

        if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flashSet('err', 'El email no es válido.');
            redirect('registro/comprador');
        }

        if ($municipioId === 0) {
            flashSet('err', 'Debes seleccionar un municipio.');
            redirect('registro/comprador');
        }

        if (strlen($comunidadColonia) === 0 || strlen($comunidadColonia) > 80) {
            flashSet('err', 'La comunidad/colonia debe tener entre 1 y 80 caracteres.');
            redirect('registro/comprador');
        }

        if (!in_array($tipoComprador, ['particular', 'tienda_negocio', 'acopiador'], true)) {
            $tipoComprador = 'particular';
        }

        if ($rfc !== null && strlen($rfc) > 13) {
            flashSet('err', 'El RFC debe tener máximo 13 caracteres.');
            redirect('registro/comprador');
        }

        $lat = null;
        $lng = null;
        if ($coordenadas !== null && strlen($coordenadas) > 0) {
            [$lat, $lng] = $this->parseCoordinates($coordenadas);
            if ($lat === null || $lng === null || !$this->isValidTabascoCoordinates($lat, $lng)) {
                flashSet('err', 'Las coordenadas no son válidas. Deben estar dentro de Tabasco.');
                redirect('registro/comprador');
            }
        }

        if (Usuario::existeTelefono($telefono)) {
            flashSet('err', 'Este teléfono ya está registrado.');
            redirect('registro/comprador');
        }

        if ($email !== null && Usuario::existeEmail($email)) {
            flashSet('err', 'Este email ya está registrado.');
            redirect('registro/comprador');
        }

        try {
            $pdo = Database::connection();
            $pdo->beginTransaction();

            $pinHash = password_hash($pin, PASSWORD_BCRYPT, ['cost' => 12]);
            $usuarioId = Usuario::crear(1, $telefono, $nombre, $apellidos, $email, $pinHash);

            if ($usuarioId === 0) {
                throw new \RuntimeException('No se obtuvo el ID del usuario.');
            }

            Comprador::crear($usuarioId, $municipioId, $comunidadColonia, $tipoComprador, $rfc, $lat, $lng);

            $pdo->commit();

            $usuario = Usuario::findById($usuarioId);
            Auth::iniciar($usuario);
            redirect('cuenta');
        } catch (\Throwable $exception) {
            try {
                Database::connection()->rollBack();
            } catch (PDOException) {
            }
            flashSet('err', 'Error: ' . $exception->getMessage());
            redirect('registro/comprador');
        }
    }

    public function mostrarVivero(): void
    {
        if (Auth::estaAutenticado()) {
            redirect('cuenta');
        }
        try {
            $municipios = Municipio::listAll();
            $comunidades = Comunidad::listAll();
        } catch (DatabaseException | PDOException $exception) {
            $municipios = [];
            $comunidades = [];
        }
        echo View::render('registro/vivero', [
            'municipios' => $municipios,
            'comunidades' => $comunidades,
            'activo' => 'registro',
        ]);
    }

    public function crearVivero(): void
    {
        if (Auth::estaAutenticado()) {
            redirect('registro');
        }

        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            flashSet('err', 'La página expiró, inténtalo de nuevo.');
            redirect('registro/vivero');
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $apellidos = trim((string) ($_POST['apellidos'] ?? ''));
        $telefono = trim((string) ($_POST['telefono'] ?? ''));
        $pin = (string) ($_POST['pin'] ?? '');
        $pinConfirmacion = (string) ($_POST['pin_confirmacion'] ?? '');
        $email = trim((string) ($_POST['email'] ?? '')) ?: null;
        $nombreVivero = trim((string) ($_POST['nombre_vivero'] ?? ''));
        $municipioId = (int) ($_POST['municipio_id'] ?? 0);
        $comunidadId = (int) ($_POST['comunidad_id'] ?? 0);
        $historia = trim((string) ($_POST['historia'] ?? '')) ?: null;
        $integrantes = (int) ($_POST['integrantes'] ?? 0) ?: null;
        $coordenadas = trim((string) ($_POST['coordenadas'] ?? ''));
        $aceptoTerminos = (bool) ($_POST['acepto_terminos'] ?? false);

        if (!$aceptoTerminos) {
            flashSet('err', 'Debes aceptar los términos de uso.');
            redirect('registro/vivero');
        }

        if (strlen($nombre) === 0 || strlen($nombre) > 60) {
            flashSet('err', 'El nombre debe tener entre 1 y 60 caracteres.');
            redirect('registro/vivero');
        }

        if (strlen($apellidos) === 0 || strlen($apellidos) > 80) {
            flashSet('err', 'Los apellidos deben tener entre 1 y 80 caracteres.');
            redirect('registro/vivero');
        }

        if (strlen($telefono) !== 10 || !ctype_digit($telefono)) {
            flashSet('err', 'El teléfono debe tener 10 dígitos.');
            redirect('registro/vivero');
        }

        if (strlen($pin) !== 6 || !ctype_digit($pin)) {
            flashSet('err', 'El PIN debe tener 6 dígitos.');
            redirect('registro/vivero');
        }

        if ($pin !== $pinConfirmacion) {
            flashSet('err', 'La confirmación del PIN no coincide.');
            redirect('registro/vivero');
        }

        if ($email !== null && strlen($email) > 120) {
            flashSet('err', 'El email debe tener máximo 120 caracteres.');
            redirect('registro/vivero');
        }

        if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flashSet('err', 'El email no es válido.');
            redirect('registro/vivero');
        }

        if (strlen($nombreVivero) === 0 || strlen($nombreVivero) > 80) {
            flashSet('err', 'El nombre del vivero debe tener entre 1 y 80 caracteres.');
            redirect('registro/vivero');
        }

        if ($municipioId === 0) {
            flashSet('err', 'Debes seleccionar un municipio.');
            redirect('registro/vivero');
        }

        if ($comunidadId === 0) {
            flashSet('err', 'Debes seleccionar una comunidad.');
            redirect('registro/vivero');
        }

        if ($historia !== null && strlen($historia) > 1000) {
            flashSet('err', 'La historia debe tener máximo 1000 caracteres.');
            redirect('registro/vivero');
        }

        if ($integrantes !== null && $integrantes <= 0) {
            $integrantes = null;
        }

        if (strlen($coordenadas) === 0) {
            flashSet('err', 'Las coordenadas son obligatorias para el vivero.');
            redirect('registro/vivero');
        }

        [$lat, $lng] = $this->parseCoordinates($coordenadas);
        if ($lat === null || $lng === null || !$this->isValidTabascoCoordinates($lat, $lng)) {
            flashSet('err', 'Las coordenadas no son válidas. Deben estar dentro de Tabasco (lat 14–33, lng -118–-86).');
            redirect('registro/vivero');
        }

        $comunidad = Comunidad::findById($comunidadId);
        if ($comunidad === null || (int) $comunidad['municipio_id'] !== $municipioId) {
            flashSet('err', 'La comunidad seleccionada no es válida.');
            redirect('registro/vivero');
        }

        if (Usuario::existeTelefono($telefono)) {
            flashSet('err', 'Este teléfono ya está registrado.');
            redirect('registro/vivero');
        }

        if ($email !== null && Usuario::existeEmail($email)) {
            flashSet('err', 'Este email ya está registrado.');
            redirect('registro/vivero');
        }

        try {
            $pdo = Database::connection();
            $pdo->beginTransaction();

            $pinHash = password_hash($pin, PASSWORD_BCRYPT, ['cost' => 12]);
            $usuarioId = Usuario::crear(2, $telefono, $nombre, $apellidos, $email, $pinHash);

            if ($usuarioId === 0) {
                throw new \RuntimeException('No se obtuvo el ID del usuario.');
            }

            Vivero::crear($usuarioId, $comunidadId, $nombreVivero, $historia, $lat, $lng, $integrantes);

            $pdo->commit();

            $usuario = Usuario::findById($usuarioId);
            Auth::iniciar($usuario);
            redirect('cuenta');
        } catch (\Throwable $exception) {
            try {
                Database::connection()->rollBack();
            } catch (PDOException) {
            }
            flashSet('err', 'Error: ' . $exception->getMessage());
            redirect('registro/vivero');
        }
    }

    private function parseCoordinates(string $text): array
    {
        $text = trim($text);
        $parts = explode(',', $text);
        if (count($parts) !== 2) {
            return [null, null];
        }

        $lat = (float) trim($parts[0]);
        $lng = (float) trim($parts[1]);

        return [$lat, $lng];
    }

    private function isValidTabascoCoordinates(float $lat, float $lng): bool
    {
        return $lat >= 14 && $lat <= 33 && $lng >= -118 && $lng <= -86;
    }
}
