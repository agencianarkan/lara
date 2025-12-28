<?php
/**
 * Test para verificar rutas desde public/
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test desde public/</h1>";

echo "<h2>Rutas de archivos</h2>";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script actual: " . __FILE__ . "<br>";
echo "Directorio actual: " . __DIR__ . "<br>";

echo "<h2>Verificando archivos desde public/</h2>";

$files = [
    '../.env' => 'Archivo .env',
    '../storage/logs/laravel.log' => 'Log de Laravel',
    '../bootstrap/app.php' => 'bootstrap/app.php',
    '../vendor/autoload.php' => 'vendor/autoload.php',
    '../public/index.php' => 'public/index.php (desde raíz)',
    'index.php' => 'index.php (actual)',
];

foreach ($files as $file => $desc) {
    $path = strpos($file, '../') === 0 
        ? dirname(__DIR__) . '/' . substr($file, 3)
        : __DIR__ . '/' . $file;
    
    $exists = file_exists($path);
    echo ($exists ? '✅' : '❌') . " $desc<br>";
    if (!$exists) {
        echo "   Ruta buscada: $path<br>";
    }
}

echo "<h2>Probando carga de Laravel</h2>";

try {
    // Fix PSR
    if (extension_loaded('psr')) {
        require_once dirname(__DIR__) . '/bootstrap/psr-fix.php';
        echo "✅ Fix PSR cargado<br>";
    }
    
    // Autoloader
    require_once dirname(__DIR__) . '/vendor/autoload.php';
    echo "✅ Autoloader cargado<br>";
    
    // Laravel app
    $app = require_once dirname(__DIR__) . '/bootstrap/app.php';
    echo "✅ App cargado<br>";
    
    // Crear request para /admin
    $request = Illuminate\Http\Request::create('/admin', 'GET');
    echo "✅ Request creado<br>";
    
    // Habilitar captura de errores
    set_error_handler(function($severity, $message, $file, $line) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    });
    
    // Intentar manejar el request con manejo de errores
    try {
        $response = $app->handleRequest($request);
        echo "✅ Request manejado<br>";
        echo "Status: " . $response->getStatusCode() . "<br>";
        echo "Content Length: " . strlen($response->getContent()) . " bytes<br>";
    } catch (\Throwable $e) {
        restore_error_handler();
        throw $e;
    }
    restore_error_handler();
    
} catch (Error $e) {
    echo "<h3 style='color: red;'>❌ ERROR FATAL</h3>";
    echo "<pre style='background: #fee; padding: 10px; border: 2px solid red;'>";
    echo "Mensaje: " . htmlspecialchars($e->getMessage()) . "\n";
    echo "Archivo: " . htmlspecialchars($e->getFile()) . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "\nTrace completo:\n" . htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
} catch (Exception $e) {
    echo "<h3 style='color: orange;'>⚠️ EXCEPCIÓN</h3>";
    echo "<pre style='background: #ffe; padding: 10px; border: 2px solid orange;'>";
    echo "Mensaje: " . htmlspecialchars($e->getMessage()) . "\n";
    echo "Archivo: " . htmlspecialchars($e->getFile()) . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "\nTrace completo:\n" . htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
} catch (\Throwable $e) {
    echo "<h3 style='color: red;'>❌ ERROR THROWABLE</h3>";
    echo "<pre style='background: #fee; padding: 10px; border: 2px solid red;'>";
    echo "Tipo: " . get_class($e) . "\n";
    echo "Mensaje: " . htmlspecialchars($e->getMessage()) . "\n";
    echo "Archivo: " . htmlspecialchars($e->getFile()) . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "\nTrace completo:\n" . htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}

