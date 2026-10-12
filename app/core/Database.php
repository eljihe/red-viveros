<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $connection = null;

    private function __construct()
    {
    }

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $host = (string) Config::get('db.host', '127.0.0.1');
        $port = (string) Config::get('db.port', '3306');
        $name = (string) Config::get('db.database', '');
        $charset = (string) Config::get('db.charset', 'utf8mb4');
        $user = (string) Config::get('db.user', 'root');
        $pass = (string) Config::get('db.password', '');

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

        try {
            self::$connection = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]);
        } catch (PDOException $exception) {
            throw new DatabaseException(
                'No fue posible conectar a la base de datos.',
                (int) $exception->getCode(),
                $exception
            );
        }

        return self::$connection;
    }
}
