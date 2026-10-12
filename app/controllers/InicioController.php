<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\DatabaseException;
use App\Core\View;
use PDOException;

final class InicioController
{
    public function index(): void
    {
        try {
            $pdo = Database::connection();
            $municipios = (int) $pdo->query('SELECT COUNT(*) FROM municipios')->fetchColumn();
            $especies = (int) $pdo->query('SELECT COUNT(*) FROM especies')->fetchColumn();
            $error = null;
        } catch (DatabaseException | PDOException $exception) {
            $municipios = 0;
            $especies = 0;
            $error = $exception->getMessage();
        }

        echo View::render('inicio/index', [
            'municipios' => $municipios,
            'especies' => $especies,
            'error' => $error,
            'activo' => 'mapa',
        ]);
    }
}
