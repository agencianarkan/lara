<?php
/**
 * Wrapper para artisan que aplica el fix de PSR antes de ejecutar comandos
 * Uso: php artisan-fix-wrapper.php [comando artisan]
 */

// Aplicar el fix de PSR ANTES de cargar el autoloader
require __DIR__.'/bootstrap/psr-fix.php';

// Cargar el autoloader
require __DIR__.'/vendor/autoload.php';

// Cargar Laravel
$app = require_once __DIR__.'/bootstrap/app.php';

// Obtener los argumentos (omitir el nombre del script)
$argv = $_SERVER['argv'] ?? [];
array_shift($argv); // Remover 'artisan-fix-wrapper.php'

// Ejecutar artisan con los argumentos
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput($argv),
    new Symfony\Component\Console\Output\ConsoleOutput()
);

$kernel->terminate($input, $status);

exit($status);

