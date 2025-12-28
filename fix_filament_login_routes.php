<?php
/**
 * Script para verificar y corregir las rutas de login de Filament
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "=== FIX DE RUTAS DE LOGIN DE FILAMENT ===\n\n";

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

echo "1. Limpiando cachés...\n";
try {
    Artisan::call('optimize:clear');
    echo "   ✅ Cachés limpiados\n";
} catch (Exception $e) {
    echo "   ⚠️  Error: " . $e->getMessage() . "\n";
}

echo "\n2. Verificando rutas de Filament...\n";
$router = $app->make('router');
$routes = $router->getRoutes();

$adminRoutes = [];
$loginRoutes = [];

foreach ($routes as $route) {
    $uri = $route->uri();
    $name = $route->getName();
    
    if (strpos($uri, 'admin') !== false) {
        $adminRoutes[] = [
            'uri' => $uri,
            'name' => $name,
            'methods' => $route->methods(),
        ];
    }
    
    if (strpos($uri, 'login') !== false || strpos($name ?? '', 'login') !== false) {
        $loginRoutes[] = [
            'uri' => $uri,
            'name' => $name,
            'methods' => $route->methods(),
        ];
    }
}

echo "   Rutas de admin encontradas: " . count($adminRoutes) . "\n";
foreach ($adminRoutes as $route) {
    echo "      - {$route['uri']} ({$route['name']})\n";
}

echo "\n   Rutas de login encontradas: " . count($loginRoutes) . "\n";
foreach ($loginRoutes as $route) {
    echo "      - {$route['uri']} ({$route['name']})\n";
}

if (empty($loginRoutes)) {
    echo "\n   ⚠️  NO se encontraron rutas de login\n";
    echo "   Esto puede causar el error 'Route [login] not defined'\n";
}

echo "\n3. Ejecutando comandos de Filament...\n";

// Publicar assets si es necesario
try {
    Artisan::call('filament:assets');
    echo "   ✅ Assets publicados\n";
} catch (Exception $e) {
    echo "   ⚠️  Error al publicar assets: " . $e->getMessage() . "\n";
}

// Optimizar
try {
    Artisan::call('optimize');
    echo "   ✅ Aplicación optimizada\n";
} catch (Exception $e) {
    echo "   ⚠️  Error al optimizar: " . $e->getMessage() . "\n";
}

echo "\n4. Verificando nuevamente las rutas...\n";
$app->boot(); // Forzar recarga de rutas
$routes = $router->getRoutes();
$loginRoutesAfter = [];

foreach ($routes as $route) {
    $uri = $route->uri();
    $name = $route->getName();
    
    if (strpos($uri, 'admin/login') !== false || 
        (strpos($name ?? '', 'login') !== false && strpos($name ?? '', 'admin') !== false)) {
        $loginRoutesAfter[] = [
            'uri' => $uri,
            'name' => $name,
        ];
    }
}

if (!empty($loginRoutesAfter)) {
    echo "   ✅ Rutas de login encontradas:\n";
    foreach ($loginRoutesAfter as $route) {
        echo "      - {$route['uri']} ({$route['name']})\n";
    }
} else {
    echo "   ⚠️  Aún no se encuentran rutas de login\n";
    echo "\n   Posible solución: Verificar que el panel esté configurado correctamente\n";
}

echo "\n=== FIN ===\n";

