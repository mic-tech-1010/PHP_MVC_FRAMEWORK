<?php

namespace App\Middleware;

use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request)
    {
        // Example: check session or cookie for logged-in user
        if (empty($_SESSION['user'])) {
            // Not logged in
            redirect('/login');
        }

        // If authenticated, return null (allow request to continue)
        return null;
    }
}
