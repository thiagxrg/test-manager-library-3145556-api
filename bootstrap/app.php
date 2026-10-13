<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Library\Adapters\Inbound\Http\ApiExceptionRenderer;
use Library\Adapters\Inbound\Http\Middleware\EnsureRole;
use Library\Adapters\Inbound\Http\Middleware\ForceJsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['role' => EnsureRole::class]);
        $middleware->api(prepend: [ForceJsonResponse::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(fn (\Throwable $e) => ApiExceptionRenderer::render($e));
    })->create();
