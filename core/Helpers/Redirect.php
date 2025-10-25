<?php

if (!function_exists('redirect')) {
    function redirect(string $url, int $statusCode = 302)
    {
        header("Location: $url", true, $statusCode);
        die();
    }
}

if (!function_exists('back')) {
    function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: {$referer}");
        exit;
    }
}
