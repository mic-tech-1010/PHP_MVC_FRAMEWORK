<?php

namespace Core\Database;

class DB {
    protected static $pdo;

    public static function init($config) {
        self::$pdo = Connection::make($config);
    }

    public static function table($table) {
        $builder = new QueryBuilder(self::$pdo);
        return $builder->table($table);
    }

    public static function __callStatic($name, $arguments)
    {
        $builder = new QueryBuilder(self::$pdo);
        return call_user_func_array([$builder, $name], $arguments);
    }
    
}