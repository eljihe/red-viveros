<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\DatabaseException;
use App\Core\View;
use App\Models\Especie;
use App\Models\Municipio;
use App\Models\Vivero;
use PDOException;

final class InicioController
{
    public function index(): void
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

        echo View::render('inicio/index', [
            'municipios' => $municipios,
            'especies' => $especies,
            'viveros' => $viveros,
            'error' => $error,
            'activo' => 'mapa',
        ]);
    }
}
