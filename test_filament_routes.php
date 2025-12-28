<?php
/**
 * Script mejorado para diagnosticar y arreglar el 404 de Filament
 */

$logFile = __DIR__ . '/.cursor/debug.log';
if (!is_dir(dirname($logFile))) {
    mkdir(dirname($logFile), 0755, true);
}

function logInfo($message, $data = []) {
    global $logFile;
    $entry = [
        'timestamp' => time() * 1000,
        'location' => __FILE__,
        'message' => $message,
        'data' => $data,
        'sessionId' => 'debug-session',
        'runId' => 'test-routes',
    ];
    
    file_put_contents($logFile, json_encode($entry) . "\n", FILE_APPEND);
    echo "✓ $message\n";
    if (!empty($data)) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                echo "  $key: " . json_encode($value) . "\n";
            } else {
                echo "  $key: $value\n";
            }
        }
    }
}

echo "=== DIAGNÓSTICO Y FIX DE FILAMENT 404 ===\n\n";

// Aplicar fix de PSR si es necesario
if (extension_loaded('psr')) {
    require_once __DIR__ . '/bootstrap/psr-fix.php';
    logInfo("Fix de PSR aplicado", []);
}

// Cargar Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

logInfo("Laravel cargado", []);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Hipótesis 1: Verificar si el provider está registrado
logInfo("H1: Verificando providers", []);

$providers = config('app.providers', []);
$filamentInConfig = in_array('App\\Providers\\Filament\\AdminPanelProvider', $providers);
logInfo("Provider en config", ['in_config' => $filamentInConfig, 'providers_count' => count($providers)]);

// Verificar si está cargado
try {
    $loadedProviders = $app->getLoadedProviders();
    $filamentLoaded = isset($loadedProviders['App\\Providers\\Filament\\AdminPanelProvider']);
    logInfo("Provider cargado en runtime", ['loaded' => $filamentLoaded]);
} catch (Exception $e) {
    logInfo("Error al verificar providers", ['error' => $e->getMessage()]);
}

// Hipótesis 2: Limpiar caché primero
logInfo("H2: Limpiando cachés", []);

try {
    Artisan::call('optimize:clear');
    logInfo("Caché optimizado limpiado", []);
} catch (Exception $e) {
    logInfo("Error al limpiar caché", ['error' => $e->getMessage()]);
}

// Hipótesis 3: Verificar rutas ANTES de limpiar
logInfo("H3: Verificando rutas antes del fix", []);

try {
    $routes = Route::getRoutes();
    $adminRoutesBefore = [];
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'admin') !== false) {
            $adminRoutesBefore[] = $uri;
        }
    }
    logInfo("Rutas admin antes", ['count' => count($adminRoutesBefore), 'routes' => array_slice($adminRoutesBefore, 0, 5)]);
} catch (Exception $e) {
    logInfo("Error al obtener rutas", ['error' => $e->getMessage()]);
}

// Hipótesis 4: Forzar registro del provider
logInfo("H4: Forzando registro de provider", []);

try {
    // Asegurar que el provider se registre
    $app->register(\App\Providers\Filament\AdminPanelProvider::class);
    logInfo("Provider registrado manualmente", []);
} catch (Exception $e) {
    logInfo("Error al registrar provider", ['error' => $e->getMessage()]);
}

// Hipótesis 5: Publicar assets
logInfo("H5: Publicando assets de Filament", []);

try {
    Artisan::call('filament:assets');
    logInfo("Assets publicados", ['output' => Artisan::output()]);
} catch (Exception $e) {
    logInfo("Error al publicar assets", ['error' => $e->getMessage()]);
}

// Hipótesis 6: Verificar rutas DESPUÉS
logInfo("H6: Verificando rutas después del fix", []);

try {
    // Limpiar el router para forzar recarga
    $app->forgetInstance('router');
    $router = $app->make('router');
    
    // Cargar las rutas de nuevo
    $app->boot();
    
    $routes = $router->getRoutes();
    $adminRoutesAfter = [];
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'admin') !== false) {
            $adminRoutesAfter[] = [
                'uri' => $uri,
                'methods' => $route->methods(),
                'name' => $route->getName(),
            ];
        }
    }
    
    logInfo("Rutas admin después", [
        'count' => count($adminRoutesAfter),
        'routes' => $adminRoutesAfter
    ]);
    
    if (empty($adminRoutesAfter)) {
        logInfo("❌ PROBLEMA: No hay rutas de admin", []);
        echo "\n⚠️  ACCIÓN REQUERIDA:\n";
        echo "   Las rutas de Filament no se están registrando.\n";
        echo "   Esto puede ser porque:\n";
        echo "   1. Falta ejecutar: php artisan filament:install --panels\n";
        echo "   2. El provider no se está cargando correctamente\n";
        echo "   3. Hay un error en la configuración\n";
    } else {
        logInfo("✅ ÉXITO: Rutas encontradas", ['total' => count($adminRoutesAfter)]);
        echo "\n✅ Las rutas están registradas. El problema puede ser:\n";
        echo "   1. Caché del navegador - prueba en modo incógnito\n";
        echo "   2. Configuración del servidor web\n";
        echo "   3. El dominio no apunta correctamente a /public\n";
    }
} catch (Exception $e) {
    logInfo("Error al verificar rutas después", ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
}

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";
echo "Revisa: .cursor/debug.log para más detalles\n";

