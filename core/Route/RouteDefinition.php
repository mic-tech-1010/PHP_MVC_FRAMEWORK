<?php

namespace Core\Route;

class RouteDefinition
{
    private Router $router;
    private string $method;
    private string $path;

    public function __construct(Router $router, string $method, string $path)
    {
        $this->router = $router;
        $this->method = $method;
        $this->path = $path;
    }

    public function middleware(array|string $middlewares): self
    {
        $middlewares = is_array($middlewares) ? $middlewares : [$middlewares];
        $this->router->addMiddlewareToRoute($this->method, $this->path, $middlewares);
        return $this;
    }

    public function name(string $name): self
    {
        $this->router->addNamedRoute($name, $this->method, $this->path);
        return $this;
    }
}
