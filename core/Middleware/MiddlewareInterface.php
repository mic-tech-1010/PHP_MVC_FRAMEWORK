<?php

namespace Core\Middleware;

use Core\Http\Request;

interface MiddlewareInterface
{
    /**
     * Handle an incoming request.
     * 
     * @param Request $request
     * @return mixed  Return null to continue or a response (e.g. redirect) to stop.
     */
    public function handle(Request $request);
}
