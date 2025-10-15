<?php

/**autoload class */

namespace Core;

class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($className) {
            $classPath = str_replace('\\', '/', $className);
            $file = __DIR__ .'/../' . $classPath . '.php';

            if(file_exists($file)) {
                require_once $file;
            } else {
                $altfile = __DIR__ . '/../' . strtolower($classPath) . '.php';
                if(file_exists($altfile)) {
                    require_once $altfile;
                }
            }
        });
    }
}

