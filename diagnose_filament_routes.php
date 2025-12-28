<?php
/**
 * Script de diagnóstico para el problema 404 de Filament
 */

$logFile = __DIR__ . '/.cursor/debug.log';

function logInfo($message, $data = []) {
    global $logFile;
    $entry = [
        'timestamp' => time() * 1000,
        'location' => __FILE__ . ':' . __LINE__,
        'message' => $message,
        'data' => $data,
        'sessionId' => 'debug-session',
        'runId' => 'diagnosis-routes',
        'hypothesisId' => 'H1'
    ];
    
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    file_put_contents($logFile, json_encode($entry) . "\n", FILE_APPEND);
    
    echo "✓ $message\n";
    if (!empty($data)) {
        echo "  " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
}

echo "=== DIAGNÓSTICO DE RUTAS FILAMENT ===\n\n";

// Hipótesis 1: Verificar si el provider está cargado
logInfo("H1: Verificando provider de Filament", []);

// Aplicar fix de PSR
if (extension_loaded('psr')) {
    require_once __DIR__ . '/bootstrap/psr-fix.php';
}

// Cargar Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// H1: Verificar provider
$providers = $app->getLoadedProviders();
$filamentLoaded = isset($providers['App\\Providers\\Filament\\AdminPanelProvider']);
logInfo("H1: Provider cargado", ['filament_provider' => $filamentLoaded, 'all_providers' => array_keys($providers)]);

// Hipótesis 2: Verificar rutas registradas
logInfo("H2: Verificando rutas de Filament", []);

try {
    $router = $app->make('router');
    $routes = $router->getRoutes();
    $adminRoutes = [];
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'admin') !== false) {
            $adminRoutes[] = [
                'uri' => $uri,
                'methods' => $route->methods(),
                'name' => $route->getName(),
            ];
        }
    }
    
    logInfo("H2: Rutas encontradas", ['admin_routes_count' => count($adminRoutes), 'routes' => $adminRoutes]);
    
    if (empty($adminRoutes)) {
        logInfo("H2: ❌ NO se encontraron rutas de admin", []);
    } else {
        logInfo("H2: ✅ Se encontraron rutas de admin", ['count' => count($adminRoutes)]);
    }
} catch (Exception $e) {
    logInfo("H2: Error al obtener rutas", ['error' => $e->getMessage()]);
}

// Hipótesis 3: Verificar que Filament está instalado
logInfo("H3: Verificando instalación de Filament", []);

$filamentFiles = [
    'vendor/filament/filament' => 'Paquete principal',
    'app/Providers/Filament/AdminPanelProvider.php' => 'Provider',
    'app/Filament/Resources/UserResource.php' => 'Resource',
];

foreach ($filamentFiles as $file => $desc) {
    $exists = file_exists(__DIR__ . '/' . $file);
    logInfo("H3: Verificando $desc", ['file' => $file, 'exists' => $exists]);
}

// Hipótesis 4: Verificar assets publicados
logInfo("H4: Verificando assets de Filament", []);

$publicAssets = [
    'public/build/filament' => 'Assets compilados',
];

foreach ($publicAssets as $path => $desc) {
    $exists = is_dir(__DIR__ . '/' . $path);
    logInfo("H4: Verificando $desc", ['path' => $path, 'exists' => $exists]);
}

// Hipótesis 5: Verificar configuración de APP_URL
logInfo("H5: Verificando configuración", []);

$appUrl = env('APP_URL', 'not set');
$appEnv = env('APP_ENV', 'not set');
logInfo("H5: Variables de entorno", ['APP_URL' => $appUrl, 'APP_ENV' => $appEnv]);

// Hipótesis 6: Verificar si las rutas están cacheadas
logInfo("H6: Verificando caché de rutas", []);

$routeCacheFile = __DIR__ . '/bootstrap/cache/routes-v7.php';
$routeCacheExists = file_exists($routeCacheFile);
logInfo("H6: Caché de rutas", ['cached' => $routeCacheExists, 'file' => basename($routeCacheFile)]);

// Intentar listar todas las rutas disponibles
logInfo("H7: Listando todas las rutas disponibles", []);

try {
    $allRoutes = [];
    foreach ($routes as $route) {
        $allRoutes[] = [
            'uri' => $route->uri(),
            'methods' => implode('|', $route->methods()),
        ];
    }
    logInfo("H7: Total de rutas", ['count' => count($allRoutes), 'sample' => array_slice($allRoutes, 0, 10)]);
} catch (Exception $e) {
    logInfo("H7: Error", ['error' => $e->getMessage()]);
}

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";
echo "Revisa el archivo: .cursor/debug.log\n";

