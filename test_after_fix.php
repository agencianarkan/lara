<?php
/**
 * Test completo después de aplicar el fix
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "=== TEST DESPUÉS DEL FIX ===\n\n";

// 1. Verificar que el fix se aplicó
$monologPath = __DIR__ . '/vendor/monolog/monolog/src/Monolog/Logger.php';
if (file_exists($monologPath)) {
    $content = file_get_contents($monologPath);
    if (strpos($content, 'FIXED-PERMANENT') !== false) {
        echo "✅ Fix aplicado en Monolog\n";
    } else {
        echo "❌ Fix NO aplicado en Monolog\n";
        echo "   Ejecuta: php scripts/fix-monolog-psr.php\n";
    }
} else {
    echo "❌ Archivo de Monolog no encontrado\n";
}

// 2. Probar cargar Monolog
echo "\n2. Probando cargar Monolog...\n";

// Fix PSR si es necesario
if (extension_loaded('psr')) {
    require_once __DIR__ . '/bootstrap/psr-fix-aggressive.php';
}

try {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Intentar cargar Monolog
    if (class_exists('Monolog\Logger')) {
        echo "✅ Monolog\\Logger se puede cargar\n";
        
        // Intentar verificar que implementa la interfaz correcta
        try {
            $reflection = new ReflectionClass('Monolog\Logger');
            $interfaces = $reflection->getInterfaceNames();
            
            $hasCorrectInterface = false;
            foreach ($interfaces as $interface) {
                if ($interface === 'Psr\Log\LoggerInterface' || 
                    strpos($interface, 'Psr\\Log\\LoggerInterface') !== false) {
                    echo "✅ Implementa: $interface (CORRECTO)\n";
                    $hasCorrectInterface = true;
                    break;
                } elseif ($interface === 'PsrExt\Log\LoggerInterface') {
                    echo "❌ Implementa: $interface (INCORRECTO - de la extensión)\n";
                }
            }
            
            if (!$hasCorrectInterface) {
                echo "⚠️  Interfaces implementadas: " . implode(', ', $interfaces) . "\n";
            }
        } catch (Exception $e) {
            echo "⚠️  Error al inspeccionar: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ Monolog\\Logger NO se puede cargar\n";
    }
} catch (Error $e) {
    echo "❌ ERROR FATAL al cargar: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
} catch (Exception $e) {
    echo "❌ EXCEPCIÓN: " . $e->getMessage() . "\n";
}

// 3. Probar cargar Laravel completo
echo "\n3. Probando cargar Laravel y acceder a /admin...\n";

try {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    $request = Illuminate\Http\Request::create('/admin', 'GET');
    $response = $app->handleRequest($request);
    
    echo "✅ Laravel cargado y /admin responde correctamente\n";
    echo "   Status: " . $response->getStatusCode() . "\n";
    
} catch (\Throwable $e) {
    echo "❌ ERROR al cargar Laravel o /admin:\n";
    echo "   Tipo: " . get_class($e) . "\n";
    echo "   Mensaje: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    
    if (strpos($e->getMessage(), 'Monolog') !== false || 
        strpos($e->getMessage(), 'PsrExt') !== false) {
        echo "\n⚠️  El error sigue relacionado con Monolog/PSR\n";
        echo "   El fix puede no haberse aplicado correctamente\n";
    }
}

