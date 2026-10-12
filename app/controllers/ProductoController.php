<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\ImagenUploader;
use App\Core\View;
use App\Models\Categoria;
use App\Models\FotoProducto;
use App\Models\Producto;
use App\Models\Unidad;
use App\Models\Vivero;

final class ProductoController
{
    private const KM_ENTREGA_VALIDOS = [5, 10, 15, 20, 30, 50, 100];

    private function obtenerViveroPropio(): array
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

    public function mostrarForm(): void
    {
        $vivero = $this->obtenerViveroPropio();

        $id = (int) ($_GET['id'] ?? 0);
        $producto = null;
        $fotos = [];

        if ($id > 0) {
            $producto = Producto::findById($id);
            if ($producto === null || (int) $producto['vivero_id'] !== (int) $vivero['id']) {
                flashSet('err', 'Producto no encontrado.');
                redirect('cuenta');
            }
            $fotos = FotoProducto::listByProductoId($id);
        }

        $flash = flashGet();

        echo View::render('producto/formulario', [
            'producto' => $producto,
            'fotos' => $fotos,
            'categorias' => Categoria::listAll(),
            'unidades' => Unidad::listAll(),
            'kmEntregaValidos' => self::KM_ENTREGA_VALIDOS,
            'mensaje' => $flash['mensaje'],
            'tipo' => $flash['tipo'],
            'activo' => 'cuenta',
        ]);
    }

    public function guardar(): void
    {
        $vivero = $this->obtenerViveroPropio();

        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            flashSet('err', 'La página expiró, inténtalo de nuevo.');
            redirect('cuenta');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $destinoError = $id > 0 ? 'panel/productos/editar?id=' . $id : 'panel/productos/nuevo';

        if ($id > 0) {
            $existente = Producto::findById($id);
            if ($existente === null || (int) $existente['vivero_id'] !== (int) $vivero['id']) {
                flashSet('err', 'Producto no encontrado.');
                redirect('cuenta');
            }
        }

        $titulo = trim((string) ($_POST['titulo'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? '')) ?: null;
        $categoriaId = (int) ($_POST['categoria_id'] ?? 0);
        $unidadId = (int) ($_POST['unidad_id'] ?? 0);
        $precioTexto = trim((string) ($_POST['precio'] ?? ''));
        $disponibleTexto = trim((string) ($_POST['disponible'] ?? ''));
        $haceEntrega = isset($_POST['hace_entrega']);
        $entregaKm = (int) ($_POST['entrega_km'] ?? 0) ?: null;
        $entregaCosto = trim((string) ($_POST['entrega_costo'] ?? '')) ?: null;

        if ($titulo === '' || mb_strlen($titulo) > 80) {
            flashSet('err', 'El título debe tener entre 1 y 80 caracteres.');
            redirect($destinoError);
        }

        if ($descripcion !== null && mb_strlen($descripcion) > 1000) {
            flashSet('err', 'La descripción debe tener máximo 1000 caracteres.');
            redirect($destinoError);
        }

        if ($categoriaId <= 0) {
            flashSet('err', 'Selecciona una categoría.');
            redirect($destinoError);
        }

        if ($unidadId <= 0) {
            flashSet('err', 'Selecciona una unidad.');
            redirect($destinoError);
        }

        $precio = null;
        if ($precioTexto !== '') {
            if (!is_numeric($precioTexto) || (float) $precioTexto < 0) {
                flashSet('err', 'El precio debe ser un número mayor o igual a 0.');
                redirect($destinoError);
            }
            $precio = (float) $precioTexto;
        }

        $disponible = null;
        if ($disponibleTexto !== '') {
            if (!ctype_digit($disponibleTexto)) {
                flashSet('err', 'La cantidad disponible debe ser un número entero.');
                redirect($destinoError);
            }
            $disponible = (int) $disponibleTexto;
        }

        if ($haceEntrega && !in_array($entregaKm, self::KM_ENTREGA_VALIDOS, true)) {
            flashSet('err', 'Selecciona hasta cuántos km haces entregas.');
            redirect($destinoError);
        }

        if (!$haceEntrega) {
            $entregaKm = null;
            $entregaCosto = null;
        }

        if ($entregaCosto !== null && mb_strlen($entregaCosto) > 60) {
            flashSet('err', 'El costo de entrega debe tener máximo 60 caracteres.');
            redirect($destinoError);
        }

        try {
            if ($id > 0) {
                Producto::actualizar(
                    $id,
                    (int) $vivero['id'],
                    $categoriaId,
                    $unidadId,
                    $titulo,
                    $descripcion,
                    $precio,
                    $disponible,
                    $haceEntrega,
                    $entregaKm,
                    $entregaCosto
                );
                flashSet('ok', 'Producto actualizado correctamente.');
            } else {
                Producto::crear(
                    (int) $vivero['id'],
                    $categoriaId,
                    $unidadId,
                    $titulo,
                    $descripcion,
                    $precio,
                    $disponible,
                    $haceEntrega,
                    $entregaKm,
                    $entregaCosto
                );
                flashSet('ok', 'Producto publicado correctamente.');
            }
            redirect('cuenta');
        } catch (\Throwable) {
            flashSet('err', 'No se pudo guardar el producto. Intenta nuevamente.');
            redirect($destinoError);
        }
    }

    public function eliminar(): void
    {
        $vivero = $this->obtenerViveroPropio();

        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            flashSet('err', 'La página expiró, inténtalo de nuevo.');
            redirect('cuenta');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $producto = Producto::findById($id);
        if ($producto === null || (int) $producto['vivero_id'] !== (int) $vivero['id']) {
            flashSet('err', 'Producto no encontrado.');
            redirect('cuenta');
        }

        try {
            foreach (FotoProducto::listByProductoId($id) as $foto) {
                ImagenUploader::eliminar(path('uploads.productos'), $foto['archivo']);
            }
            Producto::eliminar($id, (int) $vivero['id']);
            flashSet('ok', 'Producto eliminado.');
        } catch (\Throwable) {
            flashSet('err', 'No se pudo eliminar el producto. Intenta nuevamente.');
        }

        redirect('cuenta');
    }

    public function subirFoto(): void
    {
        $vivero = $this->obtenerViveroPropio();

        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            flashSet('err', 'La página expiró, inténtalo de nuevo.');
            redirect('cuenta');
        }

        $productoId = (int) ($_POST['producto_id'] ?? 0);
        $destino = 'panel/productos/editar?id=' . $productoId;

        $producto = Producto::findById($productoId);
        if ($producto === null || (int) $producto['vivero_id'] !== (int) $vivero['id']) {
            flashSet('err', 'Producto no encontrado.');
            redirect('cuenta');
        }

        $file = $_FILES['foto'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            flashSet('err', 'No se recibió una foto válida.');
            redirect($destino);
        }

        $totalFotos = FotoProducto::contarPorProducto($productoId);
        if ($totalFotos >= 6) {
            flashSet('err', 'Ya tienes el máximo de 6 fotos. Elimina una para subir otra.');
            redirect($destino);
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            flashSet('err', 'La foto debe pesar menos de 5 MB.');
            redirect($destino);
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            flashSet('err', 'Solo se aceptan imágenes JPEG, PNG o WebP.');
            redirect($destino);
        }

        $filename = ImagenUploader::guardarRedimensionada($file['tmp_name'], path('uploads.productos'));
        if ($filename === null || FotoProducto::crear($productoId, $filename, 0) <= 0) {
            flashSet('err', 'No se pudo procesar la foto. Intenta con otra.');
            redirect($destino);
        }

        flashSet('ok', 'Foto guardada correctamente.');
        redirect($destino);
    }

    public function eliminarFoto(): void
    {
        $vivero = $this->obtenerViveroPropio();

        if (!Csrf::isValid($_POST['csrf_token'] ?? null)) {
            flashSet('err', 'La página expiró, inténtalo de nuevo.');
            redirect('cuenta');
        }

        $fotoId = (int) ($_POST['foto_id'] ?? 0);
        $productoId = (int) ($_POST['producto_id'] ?? 0);
        $destino = 'panel/productos/editar?id=' . $productoId;

        $producto = Producto::findById($productoId);
        if ($producto === null || (int) $producto['vivero_id'] !== (int) $vivero['id']) {
            flashSet('err', 'Producto no encontrado.');
            redirect('cuenta');
        }

        try {
            $fotos = FotoProducto::listByProductoId($productoId);
            $foto = null;
            foreach ($fotos as $f) {
                if ((int) $f['id'] === $fotoId) {
                    $foto = $f;
                    break;
                }
            }

            if ($foto === null) {
                flashSet('err', 'Foto no encontrada.');
                redirect($destino);
            }

            ImagenUploader::eliminar(path('uploads.productos'), $foto['archivo']);
            FotoProducto::eliminar($fotoId, $productoId);
            flashSet('ok', 'Foto eliminada.');
            redirect($destino);
        } catch (\Throwable) {
            flashSet('err', 'No se pudo eliminar la foto. Intenta nuevamente.');
            redirect($destino);
        }
    }
}
