<?php

namespace Core\Route;

/**Router class */

use Core\Http\Request;

class Router
{
    protected static $routes = [];
    protected $pathPrefix = '';
    protected $currentMiddlewares = [];
    protected $middlewares = [];
    public Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function get($path, $callback)
    {
        self::$routes['get'][$path] = $callback;
    }

    public function post($path, $handler) {}

    public function group($pathPrefix, $callback, $middleware = []) {}

    public function addMiddleware($name, $func)
    {
        $this->middlewares[$name] = $func;
    }

    public function run()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();

        $callback = self::$routes[$method][$path] ?? false;

        if ($callback === false) {
            http_response_code(404);
            return "Not Found";
        }

        // If it's a controller callback like [Controller::class, 'method']
        if (is_array($callback)) {
            [$controller, $methodName] = $callback;

            // Instantiate controller
            $controllerInstance = new $controller();

            // Call its method
            return call_user_func([$controllerInstance, $methodName]);
        }

        return call_user_func($callback);
    }
}
