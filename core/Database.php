<?php

namespace Core;

use Core\Database\DB;

class Database {
    protected static $config;

    public static function configure(array $config) {
        self::$config = $config;
    }

    public static function init() {
        DB::init(self::$config);
    }
}