<?php
/**
 * Script para arreglar el problema 404 de Filament
 */

// Aplicar fix de PSR
if (extension_loaded('psr')) {
    require_once __DIR__ . '/bootstrap/psr-fix.php';
}

// Cargar Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "=== FIX PARA ERROR 404 DE FILAMENT ===\n\n";

echo "1. Limpiando caché...\n";

try {
    Artisan::call('config:clear');
    echo "   ✅ Config cache limpiado\n";
} catch (Exception $e) {
    echo "   ⚠️  Error: " . $e->getMessage() . "\n";
}

try {
    Artisan::call('route:clear');
    echo "   ✅ Route cache limpiado\n";
} catch (Exception $e) {
    echo "   ⚠️  Error: " . $e->getMessage() . "\n";
}

try {
    Artisan::call('cache:clear');
    echo "   ✅ Application cache limpiado\n";
} catch (Exception $e) {
    echo "   ⚠️  Error: " . $e->getMessage() . "\n";
}

try {
    Artisan::call('view:clear');
    echo "   ✅ View cache limpiado\n";
} catch (Exception $e) {
    echo "   ⚠️  Error: " . $e->getMessage() . "\n";
}

echo "\n2. Publicando assets de Filament...\n";
try {
    Artisan::call('filament:assets');
    echo "   ✅ Assets publicados\n";
} catch (Exception $e) {
    echo "   ⚠️  Error: " . $e->getMessage() . "\n";
}

echo "\n3. Optimizando autoloader...\n";
try {
    Artisan::call('optimize:clear');
    echo "   ✅ Optimización limpiada\n";
} catch (Exception $e) {
    echo "   ⚠️  Error: " . $e->getMessage() . "\n";
}

echo "\n4. Verificando rutas...\n";
try {
    $router = $app->make('router');
    $routes = $router->getRoutes();
    $adminRoutes = [];
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'admin') !== false) {
            $adminRoutes[] = $uri;
        }
    }
    
    if (empty($adminRoutes)) {
        echo "   ❌ NO se encontraron rutas de admin\n";
        echo "   ⚠️  El provider puede no estar registrado correctamente\n";
    } else {
        echo "   ✅ Se encontraron " . count($adminRoutes) . " rutas de admin:\n";
        foreach (array_slice($adminRoutes, 0, 5) as $route) {
            echo "      - /$route\n";
        }
    }
} catch (Exception $e) {
    echo "   ⚠️  Error al verificar rutas: " . $e->getMessage() . "\n";
}

echo "\n✅ Proceso completado\n";
echo "\n⚠️  Si las rutas aún no aparecen, ejecuta:\n";
echo "   php diagnose_filament_routes.php\n";

