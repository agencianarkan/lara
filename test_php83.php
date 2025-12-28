<?php
/**
 * Test para verificar que todo funciona con PHP 8.3
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "=== TEST CON PHP 8.3 ===\n\n";

echo "1. Verificando versión de PHP:\n";
echo "   Versión: " . PHP_VERSION . "\n";
echo "   SAPI: " . PHP_SAPI . "\n";

echo "\n2. Verificando extensión PSR:\n";
if (extension_loaded('psr')) {
    echo "   ⚠️  Extensión PSR cargada\n";
} else {
    echo "   ✅ Extensión PSR NO cargada (esto es bueno)\n";
}

echo "\n3. Cargando autoloader...\n";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "   ✅ Autoloader cargado\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n4. Verificando Monolog...\n";
try {
    if (class_exists('Monolog\Logger')) {
        echo "   ✅ Monolog\\Logger disponible\n";
        
        // Intentar crear instancia
        $logger = new Monolog\Logger('test');
        echo "   ✅ Instancia de Monolog creada exitosamente\n";
    } else {
        echo "   ❌ Monolog\\Logger NO disponible\n";
    }
} catch (Error $e) {
    echo "   ❌ ERROR FATAL: " . $e->getMessage() . "\n";
    echo "      Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "   ❌ EXCEPCIÓN: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n5. Probando cargar Laravel...\n";
try {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "   ✅ App cargado\n";
    
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo "   ✅ Kernel bootstrap completado\n";
    
    $request = Illuminate\Http\Request::create('/admin', 'GET');
    $response = $app->handleRequest($request);
    
    echo "   ✅ Request a /admin manejado exitosamente\n";
    echo "   Status Code: " . $response->getStatusCode() . "\n";
    
    if ($response->getStatusCode() === 200) {
        echo "\n✅✅✅ TODO FUNCIONA CORRECTAMENTE ✅✅✅\n";
        echo "\nAhora puedes acceder a: https://lara.narkan.cl/admin\n";
    } else {
        echo "\n⚠️  Status code no es 200, pero no hay error fatal\n";
    }
    
} catch (\Throwable $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
    echo "      Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}

