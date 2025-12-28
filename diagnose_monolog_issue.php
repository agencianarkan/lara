<?php
/**
 * Script de diagnóstico para el problema de Monolog
 * Ejecutar: php diagnose_monolog_issue.php
 */

$logFile = __DIR__ . '/.cursor/debug.log';
$logData = [];

function logInfo($message, $data = []) {
    global $logFile, $logData;
    $entry = [
        'timestamp' => time() * 1000,
        'location' => __FILE__ . ':' . __LINE__,
        'message' => $message,
        'data' => $data,
        'sessionId' => 'debug-session',
        'runId' => 'diagnosis',
        'hypothesisId' => 'H1'
    ];
    $logData[] = $entry;
    
    // Escribir a archivo NDJSON
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

echo "=== DIAGNÓSTICO DE MONOLOG ===\n\n";

// Hipótesis 1: Verificar versión de PHP
logInfo("H1: Verificando versión de PHP", ['version' => PHP_VERSION, 'sapi' => PHP_SAPI]);

// Hipótesis 2: Verificar si existe extensión psr-ext/log
if (extension_loaded('psr')) {
    logInfo("H2: Extensión PSR detectada", ['loaded' => true, 'functions' => get_extension_funcs('psr')]);
} else {
    logInfo("H2: Extensión PSR no detectada", ['loaded' => false]);
}

// Hipótesis 3: Verificar si existe la clase PsrExt\Log\LoggerInterface
if (class_exists('PsrExt\Log\LoggerInterface')) {
    logInfo("H3: Clase PsrExt\\Log\\LoggerInterface encontrada", ['exists' => true]);
    try {
        $reflection = new ReflectionClass('PsrExt\Log\LoggerInterface');
        $method = $reflection->getMethod('emergency');
        logInfo("H3: Método emergency signature", ['params' => array_map(function($p) { return $p->getName(); }, $method->getParameters())]);
    } catch (Exception $e) {
        logInfo("H3: Error al inspeccionar", ['error' => $e->getMessage()]);
    }
} else {
    logInfo("H3: Clase PsrExt\\Log\\LoggerInterface NO encontrada", ['exists' => false]);
}

// Hipótesis 4: Verificar si existe la clase Psr\Log\LoggerInterface
if (class_exists('Psr\Log\LoggerInterface')) {
    logInfo("H4: Clase Psr\\Log\\LoggerInterface encontrada", ['exists' => true]);
    try {
        $reflection = new ReflectionClass('Psr\Log\LoggerInterface');
        $method = $reflection->getMethod('emergency');
        logInfo("H4: Método emergency signature", ['params' => array_map(function($p) { return $p->getName(); }, $method->getParameters())]);
    } catch (Exception $e) {
        logInfo("H4: Error al inspeccionar", ['error' => $e->getMessage()]);
    }
} else {
    logInfo("H4: Clase Psr\\Log\\LoggerInterface NO encontrada", ['exists' => false]);
}

// Hipótesis 5: Verificar composer vendor
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    logInfo("H5: Autoloader cargado", ['exists' => true]);
    
    // Verificar monolog
    if (class_exists('Monolog\Logger')) {
        logInfo("H5: Monolog\\Logger existe", ['exists' => true]);
    } else {
        logInfo("H5: Monolog\\Logger NO existe", ['exists' => false]);
    }
} else {
    logInfo("H5: vendor/autoload.php no encontrado", ['exists' => false]);
}

// Verificar extensión específica psr-ext/log
$loadedExtensions = get_loaded_extensions();
$psrExtensions = array_filter($loadedExtensions, function($ext) {
    return stripos($ext, 'psr') !== false;
});
logInfo("Extensiones relacionadas con PSR", ['extensions' => array_values($psrExtensions)]);

// Verificar composer.json/composer.lock
if (file_exists(__DIR__ . '/composer.lock')) {
    $lock = json_decode(file_get_contents(__DIR__ . '/composer.lock'), true);
    if ($lock) {
        foreach ($lock['packages'] ?? [] as $pkg) {
            if ($pkg['name'] === 'monolog/monolog') {
                logInfo("Monolog en composer.lock", ['version' => $pkg['version'], 'requires' => $pkg['require'] ?? []]);
            }
            if ($pkg['name'] === 'psr/log') {
                logInfo("PSR Log en composer.lock", ['version' => $pkg['version']]);
            }
        }
    }
}

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";
echo "Revisa el archivo: .cursor/debug.log\n";

