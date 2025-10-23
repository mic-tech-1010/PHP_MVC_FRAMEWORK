<?php

use Core\Http\Session;
use Core\Support\ErrorBag;

if (!function_exists('old')) {
    /**
     * Retrieve old input flashed to the session.
     * Works by reading the global snapshot set by View::render().
     */
    function old(string $key, $default = '')
    {
        $old = $GLOBALS['___old'] ?? [];
        return $old[$key] ?? $default;
    }
}

if (!function_exists('errors')) {
    /**
     * Return the ErrorBag instance available for this request/view.
     */
    function errors(): ErrorBag
    {
        return $GLOBALS['___errors'] ?? new ErrorBag([]);
    }
}
