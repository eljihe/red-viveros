<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\DatabaseException;
use PDOException;

final class Unidad
{
    public static function listAll(): array
    {
        try {
            $pdo = Database::connection();
            $stmt = $pdo->query('SELECT id, nombre, plural FROM unidades ORDER BY nombre ASC');
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (DatabaseException | PDOException) {
            return [];
        }
    }
}
