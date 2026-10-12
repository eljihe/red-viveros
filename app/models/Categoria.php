<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\DatabaseException;
use PDOException;

final class Categoria
{
    public static function listAll(): array
    {
        try {
            $pdo = Database::connection();
            $stmt = $pdo->query('SELECT id, nombre FROM categorias ORDER BY orden ASC, nombre ASC');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (DatabaseException | PDOException) {
            return [];
        }
    }
}
