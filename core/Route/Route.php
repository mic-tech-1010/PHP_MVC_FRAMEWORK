<?php

namespace Core\Route;

use Core\Route\Router;
use Core\Http\Request;

class Route
{
    public static Request $request;

    public function __construct(Request $request)
    {
        self::$request = $request;
    }

    public static function init()
    {
        $route = new Router(self::$request);
        echo $route->run();
    }

    public static function __callStatic($name, $arguments)
    {
        $route = new Router(self::$request);
        return call_user_func_array([$route, $name], $arguments);
    }
    
}
