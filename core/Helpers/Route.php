<?php

use Core\Route\Route;
use Core\Route\Router;

if (!function_exists('route')) {
    function route(string $name, array $params = []): ?string
    {
        $router = Route::getRouter(); 

        if (!$router instanceof Router) {
            throw new Exception("Router instance not found. Make sure it's initialized via Route::init().");
        }

        $path = $router->route($name, $params);

        if ($path === null) {
            throw new Exception("Named route '{$name}' not found.");
        }

        return $path;
    }
}
