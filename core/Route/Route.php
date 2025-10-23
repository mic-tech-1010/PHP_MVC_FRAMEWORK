<?php

namespace Core\Route;

use Core\Http\Request;

class Route
{
    protected static ?Router $router = null;
    protected static Request $request;

    public function __construct(Request $request)
    {
        self::$request = $request;
        if (!self::$router) {
            self::$router = new Router($request);
        }
    }

    public static function init()
    {
        echo self::$router->run();
    }

    public static function getRouter(): ?Router
    {
        return self::$router;
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([self::$router, $name], $arguments);
    }
}
