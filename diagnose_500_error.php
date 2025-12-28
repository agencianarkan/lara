<?php
/**
 * Script para diagnosticar el error 500
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
        'runId' => 'diagnosis-500',
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

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== DIAGNÓSTICO DE ERROR 500 ===\n\n";

// Hipótesis 1: Verificar logs de Laravel
logInfo("H1: Verificando logs de Laravel", []);

$logPath = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logPath)) {
    $logContent = file_get_contents($logPath);
    $lines = explode("\n", $logContent);
    $recentLines = array_slice($lines, -50); // Últimas 50 líneas
    logInfo("Últimas líneas del log", ['lines' => $recentLines, 'total_lines' => count($lines)]);
    
    // Buscar errores recientes
    $errors = array_filter($recentLines, function($line) {
        return stripos($line, 'error') !== false || 
               stripos($line, 'exception') !== false ||
               stripos($line, 'fatal') !== false;
    });
    
    if (!empty($errors)) {
        logInfo("Errores encontrados en log", ['errors' => array_values($errors)]);
    }
} else {
    logInfo("Log de Laravel no existe", ['path' => $logPath]);
}

// Hipótesis 2: Probar cargar Laravel paso a paso
logInfo("H2: Probando carga de Laravel", []);

try {
    // Aplicar fix de PSR
    if (extension_loaded('psr')) {
        require_once __DIR__ . '/bootstrap/psr-fix.php';
        logInfo("Fix de PSR aplicado", []);
    }
    
    // Cargar autoloader
    require_once __DIR__ . '/vendor/autoload.php';
    logInfo("Autoloader cargado", []);
    
    // Cargar Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    logInfo("App bootstrap cargado", []);
    
    // Bootstrap kernel
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    logInfo("Kernel obtenido", []);
    
    $kernel->bootstrap();
    logInfo("Kernel bootstrap completado", []);
    
    // Probar obtener el router
    $router = $app->make('router');
    logInfo("Router obtenido", []);
    
    // Probar obtener la ruta /admin
    try {
        $route = $router->getRoutes()->match(
            Illuminate\Http\Request::create('/admin', 'GET')
        );
        logInfo("Ruta /admin encontrada", ['name' => $route->getName()]);
    } catch (Exception $e) {
        logInfo("Error al encontrar ruta /admin", ['error' => $e->getMessage()]);
    }
    
} catch (Error $e) {
    logInfo("❌ ERROR FATAL al cargar Laravel", [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => explode("\n", $e->getTraceAsString())
    ]);
} catch (Exception $e) {
    logInfo("❌ EXCEPCIÓN al cargar Laravel", [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => explode("\n", $e->getTraceAsString())
    ]);
}

// Hipótesis 3: Verificar permisos
logInfo("H3: Verificando permisos", []);

$dirs = [
    'storage' => '0755',
    'storage/logs' => '0755',
    'storage/framework' => '0755',
    'storage/framework/cache' => '0755',
    'storage/framework/sessions' => '0755',
    'storage/framework/views' => '0755',
    'bootstrap/cache' => '0755',
];

foreach ($dirs as $dir => $expectedPerms) {
    $fullPath = __DIR__ . '/' . $dir;
    if (is_dir($fullPath)) {
        $perms = substr(sprintf('%o', fileperms($fullPath)), -4);
        $writable = is_writable($fullPath);
        logInfo("Permisos de $dir", ['perms' => $perms, 'writable' => $writable]);
        
        if (!$writable) {
            logInfo("⚠️  $dir NO es escribible", []);
        }
    } else {
        logInfo("⚠️  $dir NO existe", []);
    }
}

// Hipótesis 4: Verificar configuración
logInfo("H4: Verificando configuración", []);

if (file_exists(__DIR__ . '/.env')) {
    logInfo(".env existe", []);
    
    $env = file_get_contents(__DIR__ . '/.env');
    if (strpos($env, 'APP_KEY=') !== false && strpos($env, 'APP_KEY=') < strpos($env, 'APP_KEY=base64:')) {
        logInfo("APP_KEY configurado", []);
    } else {
        logInfo("⚠️  APP_KEY puede no estar configurado", []);
    }
} else {
    logInfo("❌ .env NO existe", []);
}

// Hipótesis 5: Verificar si hay error de sintaxis en archivos críticos
logInfo("H5: Verificando sintaxis PHP", []);

$criticalFiles = [
    'bootstrap/app.php',
    'bootstrap/psr-fix.php',
    'app/Providers/Filament/AdminPanelProvider.php',
    'public/index.php',
];

foreach ($criticalFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        $output = [];
        $return = 0;
        exec("php -l $fullPath 2>&1", $output, $return);
        
        if ($return === 0) {
            logInfo("Sintaxis OK: $file", []);
        } else {
            logInfo("❌ Error de sintaxis en $file", ['error' => implode("\n", $output)]);
        }
    }
}

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";
echo "Revisa: .cursor/debug.log y storage/logs/laravel.log\n";

