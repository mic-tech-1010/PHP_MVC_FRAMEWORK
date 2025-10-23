<?php

namespace Core\Route;

use Core\Http\Request;

class Router
{
    protected static array $routes = [
        'get' => [],
        'post' => []
    ];

    protected array $groupStack = [];
    protected array $middlewareRegistry = [];
    protected Request $request;
    protected array $namedRoutes = [];


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /*----------------------------------------------
     | BASIC ROUTES
     ----------------------------------------------*/

    public function get(string $path, $callback): RouteDefinition
    {
        $fullPath = $this->applyGroupPrefix($path);
        $entry = $this->makeRouteEntry($callback, $path);
        self::$routes['get'][$fullPath] = $entry;

        return new RouteDefinition($this, 'get', $fullPath);
    }

    public function post(string $path, $callback): RouteDefinition
    {
        $fullPath = $this->applyGroupPrefix($path);
        $entry = $this->makeRouteEntry($callback, $path);
        self::$routes['post'][$fullPath] = $entry;

        return new RouteDefinition($this, 'post', $fullPath);
    }

    /*----------------------------------------------
     | GROUPS
     ----------------------------------------------*/

    public function group($attributesOrCallback, $maybeCallback = null)
    {
        if (is_callable($attributesOrCallback) && $maybeCallback === null) {
            $attributes = [];
            $callback = $attributesOrCallback;
        } else {
            $attributes = is_array($attributesOrCallback) ? $attributesOrCallback : [];
            $callback = $maybeCallback;
        }

        $normalized = $this->normalizeGroupAttributes($attributes);
        $this->groupStack[] = $normalized;

        try {
            $callback();
        } finally {
            array_pop($this->groupStack);
        }
    }

    public function middleware(array|string $middlewares)
    {
        $middlewares = is_array($middlewares) ? $middlewares : [$middlewares];

        return new class($this, $middlewares)
        {
            private Router $router;
            private array $middlewares;

            public function __construct(Router $router, array $middlewares)
            {
                $this->router = $router;
                $this->middlewares = $middlewares;
            }

            public function group(array $attributes, callable $callback)
            {
                $attributes['middleware'] = array_merge(
                    $attributes['middleware'] ?? [],
                    $this->middlewares
                );

                $this->router->group($attributes, $callback);
            }
        };
    }

    /*----------------------------------------------
     | MIDDLEWARE REGISTRATION
     ----------------------------------------------*/

    public function registerMiddleware(string $name, callable|string|object $middleware)
    {
        $this->middlewareRegistry[$name] = $middleware;
    }

    public function addMiddlewareToRoute(string $method, string $path, array $middlewares): void
    {
        if (isset(self::$routes[$method][$path])) {
            self::$routes[$method][$path]['middleware'] = array_merge(
                self::$routes[$method][$path]['middleware'],
                $middlewares
            );
        }
    }

    /*----------------------------------------------
     | ROUTE RUNNER
     ----------------------------------------------*/

    public function run()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();

        $callbackEntry = null;
        $params = [];

        foreach (self::$routes[$method] as $routePath => $routeData) {
            // Convert example /users/{id} → /users/([^/]+)
            $pattern = preg_replace('#\{([^}]+)\}#', '([^/]+)', $routePath);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // remove full match
                preg_match_all('#\{([^}]+)\}#', $routePath, $paramNames);
                $paramNames = $paramNames[1] ?? [];
                $params = array_combine($paramNames, $matches);
                $callbackEntry = $routeData;
                break;
            }
        }

        if (!$callbackEntry) {
            http_response_code(404);
            return "404 Not Found";
        }

        $callback = $callbackEntry['callback'];
        $routeMiddlewares = $callbackEntry['middleware'] ?? [];

        $this->request->setParams($params);

        // Run middleware chain
        foreach ($routeMiddlewares as $mw) {
            // Named middleware (registered by key)
            if (is_string($mw) && isset($this->middlewareRegistry[$mw])) {
                $mwClass = $this->middlewareRegistry[$mw];

                if (is_string($mwClass) && class_exists($mwClass)) {
                    $instance = new $mwClass();
                    $result = $instance->handle($this->request);
                } elseif (is_callable($mwClass)) {
                    $result = $mwClass($this->request);
                }

                if (isset($result) && $result !== null) {
                    return $result;
                }
            }
            // Direct class usage in route definition
            elseif (is_string($mw) && class_exists($mw)) {
                $instance = new $mw();
                $result = $instance->handle($this->request);
                if ($result !== null) return $result;
            }
            // Anonymous middleware callable
            elseif (is_callable($mw)) {
                $result = $mw($this->request);
                if ($result !== null) return $result;
            }
        }


        // Call controller or closure
        if (is_array($callback)) {
            [$controllerClass, $methodName] = $callback;
            $controllerInstance = new $controllerClass();
            return call_user_func([$controllerInstance, $methodName], $this->request, ...array_values($params));
        }

        return call_user_func($callback, $this->request, ...array_values($params));
    }

    /*----------------------------------------------
     | ROUTE NAMING
     ----------------------------------------------*/

    public function addNamedRoute(string $name, string $method, string $path): void
    {
        $this->namedRoutes[$name] = [
            'method' => $method,
            'path' => $path
        ];
    }

    public function route(string $name, array $params = []): ?string
    {
        if (!isset($this->namedRoutes[$name])) {
            return null;
        }

        $route = $this->namedRoutes[$name]['path'];

        // Replace {key} with corresponding $params
        foreach ($params as $key => $value) {
            $route = str_replace('{' . $key . '}', $value, $route);
        }

        return $route;
    }

    /*----------------------------------------------
     | HELPERS
     ----------------------------------------------*/

    protected function applyGroupPrefix(string $path): string
    {
        $prefixes = [];

        foreach ($this->groupStack as $group) {
            if (!empty($group['prefix'])) {
                $prefixes[] = trim($group['prefix'], '/');
            }
        }

        $prefix = implode('/', array_filter($prefixes));
        $path = trim($path, '/');

        if ($prefix && $path) {
            $full = '/' . $prefix . '/' . $path;
        } elseif ($prefix) {
            $full = '/' . $prefix;
        } else {
            $full = '/' . $path;
        }

        return preg_replace('#//+#', '/', $full ?: '/');
    }

    protected function makeRouteEntry($callback, string $originalPath): array
    {
        $stackedMiddlewares = [];

        foreach ($this->groupStack as $attrs) {
            if (!empty($attrs['middleware'])) {
                $stackedMiddlewares = array_merge($stackedMiddlewares, (array)$attrs['middleware']);
            }
        }

        return [
            'callback' => $callback,
            'middleware' => $stackedMiddlewares,
            'original' => $originalPath
        ];
    }

    protected function normalizeGroupAttributes(array $attributes): array
    {
        return [
            'prefix' => $attributes['prefix'] ?? '',
            'middleware' => isset($attributes['middleware'])
                ? (array)$attributes['middleware']
                : []
        ];
    }

    /*----------------------------------------------
     | DEBUGGING
     ----------------------------------------------*/
    public function getRoutesTable(): string
    {
        $html = '
    <style>
        h2 {
            color: #4a90e2;
            font-size: 20px;
            margin-bottom: 12px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: #1e2340;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        th, td {
            padding: 10px 14px;
            text-align: left;
            border-bottom: 1px solid #2d3257;
        }
        th {
            background-color: #22274a;
            color: #9eb3ff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
        }
        tr:nth-child(even) {
            background-color: #1a1f3b;
        }
        tr:hover {
            background-color: #2a2f56;
        }
        td {
            font-size: 13px;
        }
        .method {
            font-weight: bold;
            color: #4a90e2;
        }
        .path {
            color: #e0e6ff;
        }
        .controller {
            color: #b39ddb;
        }
        .closure {
            color: #81c784;
        }
        .middleware {
            color: #ffb74d;
        }
        .none {
            color: #e0e6ff;
            font-style: italic;
        }
        .count {
          color: #6c6f85;
        }
        .route-name {
          color: #4dd0e1;
          font-weight: 500;
       }

    </style>

    <h2>Registered Routes</h2>
    <table>
       <thead>
            <tr>
               <th>#</th>
               <th>Method</th>
               <th>Path</th>
               <th>Action</th>
               <th>Name</th>
              <th>Middleware</th>
            </tr>
          </thead>
        <tbody>
    ';

        $count = 1;

        foreach (self::$routes as $method => $routes) {
            foreach ($routes as $path => $route) {
                if (is_array($route['callback'])) {
                    $action = htmlspecialchars($route['callback'][0] . '@' . $route['callback'][1]);
                    $actionClass = 'controller';
                } elseif ($route['callback'] instanceof \Closure) {
                    $action = 'Closure';
                    $actionClass = 'closure';
                } else {
                    $action = htmlspecialchars($route['callback']);
                    $actionClass = '';
                }

                $middleware = empty($route['middleware'])
                    ? '<span class="none">—</span>'
                    : '<span class="middleware">' . implode(', ', $route['middleware']) . '</span>';

                $name = '—'; // default if no name

                foreach ($this->namedRoutes as $rName => $rData) {
                    if ($rData['path'] === $path && $rData['method'] === $method) {
                        $name = '<span class="route-name">' . htmlspecialchars($rName) . '</span>';
                        break;
                    }
                }

                $html .= "
            <tr>
                <td class='count'>{$count}</td>
                <td class='method'>" . strtoupper($method) . "</td>
                <td class='path'>{$path}</td>
                <td class='{$actionClass}'>{$action}</td>
                <td>{$name}</td>
                <td>{$middleware}</td>
            </tr>";
                $count++;
            }
        }

        $html .= '
        </tbody>
    </table>
    ';

        return $html;
    }
}
