<?php

if (!function_exists('redirect')) {
    function redirect(string $url, int $statusCode = 302)
    {
        header("Location: $url", true, $statusCode);
        exit();
    }
}
