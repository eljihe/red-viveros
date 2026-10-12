<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Coordenadas;
use App\Core\HttpNotFoundException;
use App\Core\View;
use App\Models\Categoria;
use App\Models\Comprador;
use App\Models\Contacto;
use App\Models\FotoProducto;
use App\Models\Municipio;
use App\Models\Producto;
use App\Models\Vivero;

final class MercadoController
{
    public function index(): void
    {
        $filtros = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'categoria_id' => (int) ($_GET['categoria'] ?? 0),
            'municipio_id' => (int) ($_GET['municipio'] ?? 0),
            'entrega' => isset($_GET['entrega']),
        ];

        echo View::render('mercado/index', [
            'productos' => Producto::listPublico($filtros),
            'categorias' => Categoria::listAll(),
            'municipios' => Municipio::listAll(),
            'filtros' => $filtros,
            'activo' => 'mercado',
        ]);
    }

    public function ver(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $producto = $id > 0 ? Producto::findPublicoById($id) : null;

        if ($producto === null) {
            throw new HttpNotFoundException('Producto no encontrado.');
        }

        $distanciaKm = null;
        $llegaAZona = null;

        if (Auth::estaAutenticado() && Auth::rol() === 'comprador') {
            $comprador = Comprador::findByUsuarioId((int) Auth::usuarioId());
            if ($comprador !== null && $comprador['lat'] !== null && $comprador['lng'] !== null) {
                $distanciaKm = Coordenadas::distanciaKm(
                    (float) $comprador['lat'],
                    (float) $comprador['lng'],
                    (float) $producto['lat'],
                    (float) $producto['lng']
                );
                if ((int) $producto['hace_entrega'] === 1) {
                    $llegaAZona = $distanciaKm <= (float) $producto['entrega_km'];
                }
            }
        }

        echo View::render('mercado/producto', [
            'producto' => $producto,
            'fotos' => FotoProducto::listByProductoId($id),
            'masDeEsteVivero' => Producto::listMasDeEsteVivero((int) $producto['vivero_id'], $id),
            'distanciaKm' => $distanciaKm,
            'llegaAZona' => $llegaAZona,
            'contacto' => $this->resolverContacto($id),
            'activo' => 'mercado',
        ]);
    }

    public function vender(): void
    {
        if (!Auth::estaAutenticado()) {
            redirect('registro');
        }

        if (Auth::rol() === 'responsable_vivero') {
            $vivero = Vivero::findByUsuarioId((int) Auth::usuarioId());
            redirect($vivero !== null ? 'panel/productos/nuevo' : 'registro/vivero');
        }

        echo View::render('mercado/vender', ['activo' => 'mercado']);
    }

    /**
     * Decide si la ficha debe mostrar el teléfono, un aviso de límite, o el
     * candado de "necesitas estar registrado". Si corresponde, registra el
     * contacto (una sola vez por usuario+producto+día, para no inflar el
     * límite diario con refrescos de página).
     *
     * @return array{estado: string, mensaje?: string}
     */
    private function resolverContacto(int $productoId): array
    {
        if (!Auth::estaAutenticado()) {
            return ['estado' => 'sin_sesion'];
        }

        $usuarioId = (int) Auth::usuarioId();

        if (Contacto::yaContactoHoy($usuarioId, $productoId)) {
            return ['estado' => 'visible'];
        }

        $resultado = Contacto::puedeContactar($usuarioId);
        if (!$resultado['puede']) {
            return ['estado' => 'limite', 'mensaje' => (string) $resultado['razon']];
        }

        Contacto::crear($usuarioId, $productoId);
        return ['estado' => 'visible'];
    }
}
