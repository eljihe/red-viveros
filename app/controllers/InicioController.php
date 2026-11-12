<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\DatabaseException;
use App\Core\HttpNotFoundException;
use App\Core\View;
use App\Models\Especie;
use App\Models\Municipio;
use App\Models\Producto;
use App\Models\Vivero;
use PDOException;

final class InicioController
{
    public function index(): void
    {
        $archivos = glob(path('slider') . '/*.{png,jpg,jpeg,webp}', GLOB_BRACE) ?: [];
        sort($archivos);
        $fotos = array_map(
            static fn (string $archivo): string => url('assets/plugins/parallax-slider/img/public/' . basename($archivo)),
            $archivos
        );

        echo View::render('inicio/index', [
            'productos' => array_slice(Producto::listPublico(), 0, 4),
            'fotosPortada' => $fotos,
            'activo' => 'inicio',
            'plantilla' => true,
        ]);
    }

    public function mapa(): void
    {
        try {
            Database::connection();
            $municipios = Municipio::listAll();
            $especies = Especie::listAll();
            $viveros = Vivero::listAprobadosConDetalle();
            $error = null;
        } catch (DatabaseException | PDOException $exception) {
            $municipios = [];
            $especies = [];
            $viveros = [];
            $error = $exception->getMessage();
        }

        echo View::render('inicio/mapa', [
            'municipios' => $municipios,
            'especies' => $especies,
            'viveros' => $viveros,
            'error' => $error,
            'activo' => 'viveros',
            'plantilla' => true,
        ]);
    }

    public function ficha(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $vivero = $id > 0 ? Vivero::findAprobadoConDetalleById($id) : null;

        if ($vivero === null) {
            throw new HttpNotFoundException('Vivero no encontrado.');
        }

        echo View::render('vivero/ficha', [
            'vivero' => $vivero,
            'productos' => Producto::listPublico(['vivero_id' => $id]),
            'telefonoVisible' => Auth::estaAutenticado(),
            'activo' => 'viveros',
            'plantilla' => true,
        ]);
    }
}
