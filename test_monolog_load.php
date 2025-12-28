<?php
/**
 * Test específico para ver si Monolog carga correctamente
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "=== TEST DE CARGA DE MONOLOG ===\n\n";

// Paso 1: Cargar fix de PSR
if (extension_loaded('psr')) {
    echo "1. Extensión PSR detectada\n";
    require_once __DIR__ . '/bootstrap/psr-fix-aggressive.php';
    echo "   ✅ Fix de PSR aplicado\n";
} else {
    echo "1. Extensión PSR no detectada\n";
}

// Paso 2: Cargar autoloader
echo "\n2. Cargando autoloader...\n";
require_once __DIR__ . '/vendor/autoload.php';
echo "   ✅ Autoloader cargado\n";

// Paso 3: Verificar interfaces
echo "\n3. Verificando interfaces...\n";
if (interface_exists('Psr\Log\LoggerInterface', false)) {
    echo "   ✅ Psr\\Log\\LoggerInterface disponible\n";
} else {
    echo "   ❌ Psr\\Log\\LoggerInterface NO disponible\n";
}

if (interface_exists('PsrExt\Log\LoggerInterface', false)) {
    echo "   ⚠️  PsrExt\\Log\\LoggerInterface disponible (extensión)\n";
} else {
    echo "   ✅ PsrExt\\Log\\LoggerInterface NO disponible (bien)\n";
}

// Paso 4: Intentar cargar Monolog
echo "\n4. Intentando cargar Monolog\\Logger...\n";
try {
    // Verificar si la clase existe antes de usarla
    if (class_exists('Monolog\Logger', false)) {
        echo "   ✅ Monolog\\Logger ya está cargado\n";
    } else {
        echo "   Intentando cargar ahora...\n";
        // Forzar la carga
        $reflection = new ReflectionClass('Monolog\Logger');
        echo "   ✅ Monolog\\Logger se puede cargar\n";
        
        // Verificar qué interfaz implementa
        $interfaces = $reflection->getInterfaceNames();
        echo "   Interfaces implementadas:\n";
        foreach ($interfaces as $iface) {
            echo "      - $iface\n";
            if ($iface === 'PsrExt\Log\LoggerInterface') {
                echo "         ⚠️  PROBLEMA: Implementa la interfaz de la extensión!\n";
            }
        }
    }
} catch (Error $e) {
    echo "   ❌ ERROR FATAL: " . $e->getMessage() . "\n";
    echo "      Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    
    if (strpos($e->getMessage(), 'PsrExt') !== false) {
        echo "\n   ⚠️  El error sigue relacionado con PsrExt\n";
        echo "   El fix puede no estar funcionando completamente\n";
    }
} catch (Exception $e) {
    echo "   ❌ EXCEPCIÓN: " . $e->getMessage() . "\n";
}

// Paso 5: Intentar crear una instancia simple
echo "\n5. Intentando crear instancia de Monolog\\Logger...\n";
try {
    $logger = new Monolog\Logger('test');
    echo "   ✅ Instancia creada exitosamente\n";
} catch (Error $e) {
    echo "   ❌ ERROR al crear instancia: " . $e->getMessage() . "\n";
    echo "      Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
} catch (Exception $e) {
    echo "   ❌ EXCEPCIÓN: " . $e->getMessage() . "\n";
}

