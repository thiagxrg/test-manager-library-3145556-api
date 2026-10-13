<?php

namespace Library\Adapters\Inbound\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * This API never renders HTML or redirects a guest to a "login" page — every
 * route under /api/v1 answers in JSON, token-missing or not (RNF-4). Without
 * this, Laravel's default guest handling tries to redirect to a named "login"
 * route that doesn't exist on a pure API.
 */
class ForceJsonResponse
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
