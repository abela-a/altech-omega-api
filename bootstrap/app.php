<?php

use App\Http\Middleware\JsonResponseMiddleware;
use App\Http\Middleware\RequestLogMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append([
            RequestLogMiddleware::class,
            JsonResponseMiddleware::class,
        ]);

        $middleware->appendToGroup('api', [
            //
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
