<?php

namespace Core\Database;

use PDO;
use PDOException;

class Connection
{
    protected static $instance = null;

    public static function make($config)
    {
        if (!self::$instance) {
            try {
                $dsn = "mysql:host={$config['host']};dbname={$config['dbname']}";
                self::$instance = new pdo($dsn, $config['user'], $config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                ]);
            } catch (PDOException $e) {
                die("connection Failed" . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
