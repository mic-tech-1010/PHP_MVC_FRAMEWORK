<?php

namespace App\Http;

use App\Middleware\AuthMiddleware;
use Core\Route\Route;

class Kernel
{
    public static function registerMiddlewares(): void
    {
        Route::registerMiddleware('auth', AuthMiddleware::class);
        // Add more:
        // Route::registerMiddleware('admin', AdminMiddleware::class);
    }
}
