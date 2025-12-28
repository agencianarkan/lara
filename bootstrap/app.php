<?php

// Fix AGRESIVO para conflicto entre extensión psr (PHP 8.4) y Monolog
// NOTA: Este fix también se ejecuta en public/index.php ANTES del autoloader
// Este es un respaldo por si acaso
if (extension_loaded('psr')) {
    if (!class_exists('Psr\Log\LoggerInterface', false)) {
        require_once __DIR__.'/psr-fix-aggressive.php';
    }
}

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
