<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Coordenadas;
use App\Core\Csrf;
use App\Core\ImagenUploader;
use App\Core\Session;
use App\Core\View;
use App\Models\Especie;
use App\Models\FotoVivero;
use App\Models\Inventario;
use App\Models\Servicio;
use App\Models\Vivero;

final class ViveroController
{
    private function guardarVivero(): ?array
    {
        Auth::requiereSesion();
        if (Auth::rol() !== 'responsable_vivero') {
            redirect('cuenta');
        }
        $vivero = Vivero::findByUsuarioId(Auth::usuarioId());
        if ($vivero === null) {
            redirect('cuenta');
        }
        return $vivero;
    }

    public function mostrar(): void
    {
        $vivero = $this->guardarVivero();
        $especies = Especie::listAll();
        $inventario = Inventario::listByViveroId((int) $vivero['id']);
        $fotos = FotoVivero::listByViveroId((int) $vivero['id']);
        $servicios = Servicio::listByViveroId((int) $vivero['id']);
        $flash = flashGet();

        echo View::render('vivero/panel', [
            'vivero' => $vivero,
            'especies' => $especies,
            'inventario' => $inventario,
            'fotos' => $fotos,
            'servicios' => $servicios,
            'mensaje' => $flash['mensaje'],
            'tipo' => $flash['tipo'],
            'activo' => 'panel',
        ]);
    }

    public function actualizarDatos(): void
    {
        $vivero = $this->guardarVivero();

        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            flashSet('err', 'La página expiró, inténtalo de nuevo.');
            redirect('panel/vivero');
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $historia = trim((string) ($_POST['historia'] ?? '')) ?: null;
        $integrantes = (int) ($_POST['integrantes'] ?? 0) ?: null;
        $coordenadas = trim((string) ($_POST['coordenadas'] ?? ''));

        if (strlen($nombre) === 0 || strlen($nombre) > 80) {
            flashSet('err', 'El nombre debe tener entre 1 y 80 caracteres.');
            redirect('panel/vivero');
        }

        if ($historia !== null && strlen($historia) > 1000) {
            flashSet('err', 'La historia debe tener máximo 1000 caracteres.');
            redirect('panel/vivero');
        }

        if ($integrantes !== null && $integrantes <= 0) {
            $integrantes = null;
        }

        if (strlen($coordenadas) === 0) {
            flashSet('err', 'Las coordenadas son obligatorias.');
            redirect('panel/vivero');
        }

        [$lat, $lng] = Coordenadas::parse($coordenadas);
        if ($lat === null || $lng === null || !Coordenadas::esValidaTabasco($lat, $lng)) {
            flashSet('err', 'Las coordenadas no son válidas. Deben estar dentro de Tabasco (lat 14–33, lng -118–-86).');
            redirect('panel/vivero');
        }

        try {
            Vivero::actualizar((int) $vivero['id'], $nombre, $historia, $lat, $lng, $integrantes);
            flashSet('ok', 'Datos guardados correctamente.');
            redirect('panel/vivero');
        } catch (\Throwable) {
            flashSet('err', 'No se pudieron guardar los datos. Intenta nuevamente.');
            redirect('panel/vivero');
        }
    }

    public function guardarInventario(): void
    {
        $vivero = $this->guardarVivero();

        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            flashSet('err', 'La página expiró, inténtalo de nuevo.');
            redirect('panel/vivero');
        }

        $especieId = (int) ($_POST['especie_id'] ?? 0);
        $cantidad = (int) ($_POST['cantidad'] ?? 0);
        $edadMeses = (int) ($_POST['edad_meses'] ?? 0) ?: null;

        if ($especieId <= 0) {
            flashSet('err', 'Debes seleccionar una especie.');
            redirect('panel/vivero');
        }

        if ($cantidad < 0 || $cantidad > 100000) {
            flashSet('err', 'La cantidad debe estar entre 0 y 100000.');
            redirect('panel/vivero');
        }

        if ($edadMeses !== null && ($edadMeses < 0 || $edadMeses > 255)) {
            flashSet('err', 'La edad debe estar entre 0 y 255 meses.');
            redirect('panel/vivero');
        }

        try {
            Inventario::guardar((int) $vivero['id'], $especieId, $cantidad, $edadMeses);
            flashSet('ok', 'Inventario actualizado.');
            redirect('panel/vivero');
        } catch (\Throwable) {
            flashSet('err', 'No se pudo actualizar el inventario. Intenta nuevamente.');
            redirect('panel/vivero');
        }
    }

    public function eliminarInventario(): void
    {
        $vivero = $this->guardarVivero();

        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            flashSet('err', 'La página expiró, inténtalo de nuevo.');
            redirect('panel/vivero');
        }

        $especieId = (int) ($_POST['especie_id'] ?? 0);

        if ($especieId <= 0) {
            flashSet('err', 'Especie inválida.');
            redirect('panel/vivero');
        }

        try {
            Inventario::eliminar((int) $vivero['id'], $especieId);
            flashSet('ok', 'Especie removida del inventario.');
            redirect('panel/vivero');
        } catch (\Throwable) {
            flashSet('err', 'No se pudo remover la especie. Intenta nuevamente.');
            redirect('panel/vivero');
        }
    }

    public function guardarServicio(): void
    {
        $vivero = $this->guardarVivero();

        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            flashSet('err', 'La página expiró, inténtalo de nuevo.');
            redirect('panel/vivero');
        }

        $titulo = trim((string) ($_POST['titulo'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? '')) ?: null;
        $horario = trim((string) ($_POST['horario'] ?? '')) ?: null;
        $costo = trim((string) ($_POST['costo'] ?? '')) ?: null;

        if ($titulo === '' || mb_strlen($titulo) > 80) {
            flashSet('err', 'El nombre del servicio debe tener entre 1 y 80 caracteres.');
            redirect('panel/vivero');
        }

        if ($descripcion !== null && mb_strlen($descripcion) > 500) {
            flashSet('err', 'La descripción debe tener máximo 500 caracteres.');
            redirect('panel/vivero');
        }

        if ($horario !== null && mb_strlen($horario) > 120) {
            flashSet('err', 'El horario debe tener máximo 120 caracteres.');
            redirect('panel/vivero');
        }

        if ($costo !== null && mb_strlen($costo) > 60) {
            flashSet('err', 'El costo debe tener máximo 60 caracteres.');
            redirect('panel/vivero');
        }

        if (Servicio::countByViveroId((int) $vivero['id']) >= 10) {
            flashSet('err', 'Ya tienes el máximo de 10 servicios. Elimina uno para agregar otro.');
            redirect('panel/vivero');
        }

        try {
            Servicio::crear((int) $vivero['id'], $titulo, $descripcion, $horario, $costo);
            flashSet('ok', 'Servicio agregado correctamente.');
            redirect('panel/vivero');
        } catch (\Throwable) {
            flashSet('err', 'No se pudo agregar el servicio. Intenta nuevamente.');
            redirect('panel/vivero');
        }
    }

    public function eliminarServicio(): void
    {
        $vivero = $this->guardarVivero();

        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            flashSet('err', 'La página expiró, inténtalo de nuevo.');
            redirect('panel/vivero');
        }

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            flashSet('err', 'Servicio inválido.');
            redirect('panel/vivero');
        }

        try {
            Servicio::eliminar($id, (int) $vivero['id']);
            flashSet('ok', 'Servicio eliminado.');
            redirect('panel/vivero');
        } catch (\Throwable) {
            flashSet('err', 'No se pudo eliminar el servicio. Intenta nuevamente.');
            redirect('panel/vivero');
        }
    }

    public function subirFoto(): void
    {
        $vivero = $this->guardarVivero();

        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            flashSet('err', 'La página expiró, inténtalo de nuevo.');
            redirect('panel/vivero');
        }

        $file = $_FILES['foto'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            flashSet('err', 'No se recibió una foto válida.');
            redirect('panel/vivero');
        }

        $totalFotos = FotoVivero::contarPorVivero((int) $vivero['id']);
        if ($totalFotos >= 6) {
            flashSet('err', 'Ya tienes el máximo de 6 fotos. Elimina una para subir otra.');
            redirect('panel/vivero');
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            flashSet('err', 'La foto debe pesar menos de 5 MB.');
            redirect('panel/vivero');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            flashSet('err', 'Solo se aceptan imágenes JPEG, PNG o WebP.');
            redirect('panel/vivero');
        }

        $filename = ImagenUploader::guardarRedimensionada($file['tmp_name'], path('uploads.viveros'));
        if ($filename === null || FotoVivero::crear((int) $vivero['id'], $filename, 0) <= 0) {
            flashSet('err', 'No se pudo procesar la foto. Intenta con otra.');
            redirect('panel/vivero');
        }

        flashSet('ok', 'Foto guardada correctamente.');
        redirect('panel/vivero');
    }

    public function eliminarFoto(): void
    {
        $vivero = $this->guardarVivero();

        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            flashSet('err', 'La página expiró, inténtalo de nuevo.');
            redirect('panel/vivero');
        }

        $fotoId = (int) ($_POST['foto_id'] ?? 0);
        if ($fotoId <= 0) {
            flashSet('err', 'Foto inválida.');
            redirect('panel/vivero');
        }

        try {
            $fotos = FotoVivero::listByViveroId((int) $vivero['id']);
            $foto = null;
            foreach ($fotos as $f) {
                if ((int) $f['id'] === $fotoId) {
                    $foto = $f;
                    break;
                }
            }

            if ($foto === null) {
                flashSet('err', 'Foto no encontrada.');
                redirect('panel/vivero');
            }

            ImagenUploader::eliminar(path('uploads.viveros'), $foto['archivo']);
            FotoVivero::eliminar($fotoId, (int) $vivero['id']);
            flashSet('ok', 'Foto eliminada.');
            redirect('panel/vivero');
        } catch (\Throwable) {
            flashSet('err', 'No se pudo eliminar la foto. Intenta nuevamente.');
            redirect('panel/vivero');
        }
    }
}
