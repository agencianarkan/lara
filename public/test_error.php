<?php
/**
 * Script para probar si PHP funciona y mostrar errores
 * Accede desde: https://lara.narkan.cl/test_error.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test de PHP</h1>";

echo "<h2>1. PHP funciona</h2>";
echo "Versión PHP: " . PHP_VERSION . "<br>";

echo "<h2>2. Probando incluir archivos</h2>";

try {
    // Probar incluir bootstrap
    $bootstrapPath = dirname(__DIR__) . '/bootstrap/psr-fix.php';
    if (file_exists($bootstrapPath)) {
        echo "✅ bootstrap/psr-fix.php existe<br>";
        require_once $bootstrapPath;
        echo "✅ bootstrap/psr-fix.php cargado<br>";
    } else {
        echo "❌ bootstrap/psr-fix.php NO existe<br>";
    }
    
    // Probar autoloader
    $autoloadPath = dirname(__DIR__) . '/vendor/autoload.php';
    if (file_exists($autoloadPath)) {
        echo "✅ vendor/autoload.php existe<br>";
        require_once $autoloadPath;
        echo "✅ vendor/autoload.php cargado<br>";
    } else {
        echo "❌ vendor/autoload.php NO existe<br>";
    }
    
    // Probar bootstrap de Laravel
    $appPath = dirname(__DIR__) . '/bootstrap/app.php';
    if (file_exists($appPath)) {
        echo "✅ bootstrap/app.php existe<br>";
        $app = require_once $appPath;
        echo "✅ bootstrap/app.php cargado<br>";
    } else {
        echo "❌ bootstrap/app.php NO existe<br>";
    }
    
} catch (Error $e) {
    echo "<h2 style='color: red;'>❌ ERROR FATAL</h2>";
    echo "<pre>";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString();
    echo "</pre>";
} catch (Exception $e) {
    echo "<h2 style='color: orange;'>⚠️ EXCEPCIÓN</h2>";
    echo "<pre>";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString();
    echo "</pre>";
}

echo "<h2>3. Información del servidor</h2>";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'no definido') . "<br>";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'no definido') . "<br>";

echo "<h2>4. Archivos críticos</h2>";
$files = [
    '../.env' => '.env',
    '../storage/logs/laravel.log' => 'Log de Laravel',
    '../bootstrap/app.php' => 'bootstrap/app.php',
];

foreach ($files as $file => $desc) {
    $exists = file_exists(dirname(__DIR__) . '/' . $file);
    echo ($exists ? '✅' : '❌') . " $desc: " . ($exists ? 'Existe' : 'NO existe') . "<br>";
}

