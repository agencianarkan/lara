<?php
/**
 * Test directo para /admin con manejo completo de errores
 */

// Habilitar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

// Capturar output para ver qué pasa
ob_start();

try {
    // Fix PSR
    if (extension_loaded('psr')) {
        require_once dirname(__DIR__) . '/bootstrap/psr-fix.php';
    }
    
    // Autoloader
    require_once dirname(__DIR__) . '/vendor/autoload.php';
    
    // Laravel app
    $app = require_once dirname(__DIR__) . '/bootstrap/app.php';
    
    // Crear request para /admin
    $request = Illuminate\Http\Request::create('/admin', 'GET', [], [], [], $_SERVER);
    
    // Manejar el request
    $response = $app->handleRequest($request);
    
    // Si llegamos aquí, funcionó
    ob_end_clean();
    
    // Enviar la respuesta
    $response->send();
    exit;
    
} catch (\Throwable $e) {
    ob_end_clean();
    
    http_response_code(500);
    
    echo "<!DOCTYPE html><html><head><title>Error 500</title></head><body>";
    echo "<h1 style='color: red;'>Error 500 - Error al cargar /admin</h1>";
    echo "<h2>Detalles del error:</h2>";
    echo "<pre style='background: #fee; padding: 15px; border: 2px solid red; overflow: auto;'>";
    echo "<strong>Tipo:</strong> " . get_class($e) . "\n";
    echo "<strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "\n";
    echo "<strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . "\n";
    echo "<strong>Línea:</strong> " . $e->getLine() . "\n";
    echo "\n<strong>Stack Trace:</strong>\n" . htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
    
    // Información adicional
    echo "<h2>Información del entorno:</h2>";
    echo "<pre>";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "Extension PSR loaded: " . (extension_loaded('psr') ? 'Sí' : 'No') . "\n";
    echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
    echo "</pre>";
    
    echo "</body></html>";
    exit;
}

