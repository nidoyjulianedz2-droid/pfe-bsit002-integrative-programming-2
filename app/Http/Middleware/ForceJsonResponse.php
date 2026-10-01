<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Week 6 - Middleware example.
 *
 * Middleware filters HTTP requests before they reach the controller.
 * This one sets "Accept: application/json" on every API request so that
 * Laravel always answers with JSON (validation errors, 401, 404, etc.).
 */
class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
