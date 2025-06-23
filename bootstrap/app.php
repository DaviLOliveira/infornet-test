<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    // Em bootstrap/app.php
    ->withRouting(
    web: __DIR__.'/../routes/web.php',
    // api: __DIR__.'/../routes/api.php', // Linha comentada
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        
        $middleware->alias([
            'JWTFactory' => \Tymon\JWTAuth\Facades\JWTFactory::class,
            'JWTAuth'    => \Tymon\JWTAuth\Facades\JWTAuth::class,
        ]);

        

    })
    ->withProviders([
        \Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();