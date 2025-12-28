<?php
/**
 * Test después del fix radical
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "=== TEST DESPUÉS DEL FIX RADICAL ===\n\n";

// Aplicar el fix radical
require __DIR__ . '/scripts/fix-monolog-radical.php';

echo "\n=== Probando carga de Monolog ===\n";

// NO aplicar el fix agresivo - puede causar conflictos
// if (extension_loaded('psr')) {
//     require_once __DIR__ . '/bootstrap/psr-fix-aggressive.php';
// }

try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "✅ Autoloader cargado\n";
    
    // Intentar cargar Monolog
    if (class_exists('Monolog\Logger')) {
        echo "✅ Monolog\\Logger se puede cargar\n";
        
        // Crear instancia de prueba
        $logger = new Monolog\Logger('test');
        echo "✅ Instancia de Monolog creada exitosamente\n";
        
    } else {
        echo "❌ Monolog\\Logger NO se puede cargar\n";
    }
} catch (Error $e) {
    echo "❌ ERROR FATAL: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    
    if (strpos($e->getMessage(), 'PsrExt') !== false) {
        echo "\n⚠️  El error sigue relacionado con PsrExt\n";
        echo "   La extensión PHP 8.4 está causando el conflicto\n";
        echo "   SOLUCIÓN PERMANENTE: Contactar al hosting para:\n";
        echo "   - Deshabilitar la extensión psr, o\n";
        echo "   - Cambiar a PHP 8.3 o 8.2\n";
    }
} catch (Exception $e) {
    echo "❌ EXCEPCIÓN: " . $e->getMessage() . "\n";
}

