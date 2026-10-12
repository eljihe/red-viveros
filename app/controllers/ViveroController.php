<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Coordenadas;
use App\Core\Csrf;
use App\Core\Session;
use App\Core\View;
use App\Models\Especie;
use App\Models\FotoVivero;
use App\Models\Inventario;
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
        $flash = flashGet();

        echo View::render('vivero/panel', [
            'vivero' => $vivero,
            'especies' => $especies,
            'inventario' => $inventario,
            'fotos' => $fotos,
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

        if (!$this->redimensionarGuardar($file['tmp_name'], (int) $vivero['id'])) {
            flashSet('err', 'No se pudo procesar la foto. Intenta con otra.');
            redirect('panel/vivero');
        }

        flashSet('ok', 'Foto guardada correctamente.');
        redirect('panel/vivero');
    }

    private function redimensionarGuardar(string $tmpFile, int $viveroId): bool
    {
        try {
            if (!extension_loaded('gd')) {
                return false;
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmpFile);
            finfo_close($finfo);

            $source = match ($mime) {
                'image/png' => imagecreatefrompng($tmpFile),
                'image/webp' => imagecreatefromwebp($tmpFile),
                default => imagecreatefromjpeg($tmpFile),
            };

            if ($source === false) {
                return false;
            }

            $width = imagesx($source);
            $height = imagesy($source);

            if ($width > 1200 || $height > 1200) {
                $maxDim = max($width, $height);
                $ratio = 1200 / $maxDim;
                $newWidth = (int) ($width * $ratio);
                $newHeight = (int) ($height * $ratio);
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                if ($resized === false) {
                    imagedestroy($source);
                    return false;
                }
                imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($source);
                $source = $resized;
            }

            $filename = bin2hex(random_bytes(16)) . '.jpg';
            $uploadsDir = dirname(__DIR__, 2) . '/public/uploads/viveros';
            $path = $uploadsDir . '/' . $filename;
            imagejpeg($source, $path, 82);
            imagedestroy($source);

            $fotoId = FotoVivero::crear($viveroId, $filename, 0);
            return $fotoId > 0;
        } catch (\Throwable) {
            return false;
        }
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

            $uploadsDir = dirname(__DIR__, 2) . '/public/uploads/viveros';
            $path = $uploadsDir . '/' . basename($foto['archivo']);
            if (file_exists($path)) {
                unlink($path);
            }

            FotoVivero::eliminar($fotoId, (int) $vivero['id']);
            flashSet('ok', 'Foto eliminada.');
            redirect('panel/vivero');
        } catch (\Throwable) {
            flashSet('err', 'No se pudo eliminar la foto. Intenta nuevamente.');
            redirect('panel/vivero');
        }
    }
}
