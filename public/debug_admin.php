<?php
/**
 * Script de debug completo para capturar TODOS los errores
 */

// Habilitar TODOS los errores
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

// Registrar manejador de errores fatales
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo "<h2 style='color: red;'>FATAL ERROR CAPTURADO:</h2>";
        echo "<pre style='background: #fee; padding: 15px; border: 2px solid red;'>";
        echo "Tipo: " . $error['type'] . "\n";
        echo "Mensaje: " . htmlspecialchars($error['message']) . "\n";
        echo "Archivo: " . htmlspecialchars($error['file']) . "\n";
        echo "Línea: " . $error['line'] . "\n";
        echo "</pre>";
    }
});

// Manejar todos los errores
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo "<h3 style='color: orange;'>ERROR CAPTURADO:</h3>";
    echo "<pre style='background: #ffe; padding: 10px;'>";
    echo "Tipo: $errno\n";
    echo "Mensaje: " . htmlspecialchars($errstr) . "\n";
    echo "Archivo: " . htmlspecialchars($errfile) . "\n";
    echo "Línea: $errline\n";
    echo "</pre>";
    return false; // Continuar con el manejo normal
});

echo "<!DOCTYPE html><html><head><title>Debug Admin</title></head><body>";
echo "<h1>Debug de /admin</h1>";

try {
    echo "<h2>Paso 1: Cargando fix de PSR...</h2>";
    
    // OPCIONAL: Probar SIN el fix de PSR primero
    $skipPsrFix = isset($_GET['skip_psr']) && $_GET['skip_psr'] == '1';
    
    if (!$skipPsrFix && extension_loaded('psr')) {
        echo "✅ Cargando fix de PSR...<br>";
        require_once dirname(__DIR__) . '/bootstrap/psr-fix.php';
        echo "✅ Fix PSR cargado<br>";
    } else {
        if ($skipPsrFix) {
            echo "⏭️ Saltando fix de PSR (parámetro skip_psr=1)<br>";
        } else {
            echo "ℹ️ Extensión PSR no cargada, saltando fix<br>";
        }
    }
    
    echo "<h2>Paso 2: Cargando autoloader...</h2>";
    require_once dirname(__DIR__) . '/vendor/autoload.php';
    echo "✅ Autoloader cargado<br>";
    
    echo "<h2>Paso 3: Cargando Laravel app...</h2>";
    $app = require_once dirname(__DIR__) . '/bootstrap/app.php';
    echo "✅ App cargado<br>";
    
    echo "<h2>Paso 4: Obteniendo kernel...</h2>";
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    echo "✅ Kernel obtenido<br>";
    
    echo "<h2>Paso 5: Bootstrap del kernel...</h2>";
    $kernel->bootstrap();
    echo "✅ Kernel bootstrap completado<br>";
    
    echo "<h2>Paso 6: Creando request para /admin...</h2>";
    $request = Illuminate\Http\Request::create('/admin', 'GET', [], [], [], $_SERVER);
    echo "✅ Request creado<br>";
    echo "URI: " . $request->getRequestUri() . "<br>";
    
    echo "<h2>Paso 7: Obteniendo router...</h2>";
    $router = $app->make('router');
    echo "✅ Router obtenido<br>";
    
    echo "<h2>Paso 8: Buscando ruta /admin...</h2>";
    try {
        $routes = $router->getRoutes();
        $adminRoute = null;
        
        foreach ($routes as $route) {
            if ($route->uri() === 'admin' || strpos($route->uri(), 'admin') === 0) {
                $adminRoute = $route;
                echo "✅ Ruta encontrada: " . $route->uri() . " (nombre: " . $route->getName() . ")<br>";
                break;
            }
        }
        
        if (!$adminRoute) {
            echo "❌ NO se encontró la ruta /admin<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error al buscar ruta: " . $e->getMessage() . "<br>";
    }
    
    echo "<h2>Paso 9: MANEJANDO REQUEST (aquí es donde puede fallar)...</h2>";
    echo "<strong style='color: red;'>Si el script se detiene aquí, hay un error fatal.</strong><br>";
    
    // Capturar output buffer para ver errores
    ob_start();
    
    try {
        $response = $app->handleRequest($request);
        $output = ob_get_clean();
        
        echo "✅ Request manejado exitosamente!<br>";
        echo "Status Code: " . $response->getStatusCode() . "<br>";
        
        if (!empty($output)) {
            echo "<h3>Output capturado:</h3><pre>" . htmlspecialchars($output) . "</pre>";
        }
        
        // Mostrar contenido de respuesta
        $content = $response->getContent();
        echo "Tamaño de respuesta: " . strlen($content) . " bytes<br>";
        
        if (strlen($content) < 1000) {
            echo "<h3>Contenido de respuesta:</h3><pre>" . htmlspecialchars($content) . "</pre>";
        } else {
            echo "<p>Respuesta demasiado larga para mostrar (probablemente HTML válido)</p>";
        }
        
    } catch (\Throwable $e) {
        ob_end_clean();
        throw $e;
    }
    
} catch (\Throwable $e) {
    echo "<h2 style='color: red;'>❌ ERROR CAPTURADO:</h2>";
    echo "<pre style='background: #fee; padding: 15px; border: 2px solid red; font-size: 12px; overflow: auto; max-height: 500px;'>";
    echo "<strong>Tipo:</strong> " . get_class($e) . "\n\n";
    echo "<strong>Mensaje:</strong>\n" . htmlspecialchars($e->getMessage()) . "\n\n";
    echo "<strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . "\n";
    echo "<strong>Línea:</strong> " . $e->getLine() . "\n\n";
    echo "<strong>Stack Trace:</strong>\n" . htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
    
    // Si es el error de Monolog/PSR, sugerir probar sin el fix
    if (strpos($e->getMessage(), 'Monolog') !== false || strpos($e->getMessage(), 'PsrExt') !== false) {
        echo "<hr>";
        echo "<h3 style='color: orange;'>💡 SUGERENCIA:</h3>";
        echo "<p>El error parece estar relacionado con Monolog/PSR.</p>";
        echo "<p><a href='?skip_psr=1' style='background: orange; color: white; padding: 10px; text-decoration: none;'>Probar SIN el fix de PSR</a></p>";
    }
}

echo "</body></html>";

