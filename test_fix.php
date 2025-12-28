<?php
/**
 * Script de prueba para verificar que el fix funciona
 */

echo "=== PRUEBA DEL FIX DE MONOLOG ===\n\n";

// Verificar si la extensión está cargada (esperado en PHP 8.4)
if (extension_loaded('psr')) {
    echo "⚠️  La extensión PSR está cargada (esto es normal en PHP 8.4)\n";
} else {
    echo "✅ La extensión PSR no está cargada\n";
}

// Aplicar el fix ANTES de cargar el autoloader
echo "\n1. Aplicando fix de PSR...\n";
require_once __DIR__ . '/bootstrap/psr-fix.php';
echo "   ✅ Fix aplicado\n";

// Intentar cargar el autoloader
echo "\n2. Cargando autoloader de Composer...\n";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "   ✅ Autoloader cargado correctamente\n";
} catch (Error $e) {
    echo "   ❌ ERROR al cargar autoloader: " . $e->getMessage() . "\n";
    echo "   Línea: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "   ❌ EXCEPCIÓN: " . $e->getMessage() . "\n";
    exit(1);
}

// Verificar que las interfaces de la biblioteca están disponibles
echo "\n3. Verificando interfaces PSR...\n";
if (interface_exists('Psr\Log\LoggerInterface')) {
    echo "   ✅ Psr\\Log\\LoggerInterface disponible (biblioteca)\n";
} else {
    echo "   ❌ Psr\\Log\\LoggerInterface NO disponible\n";
    exit(1);
}

// Intentar cargar Monolog
echo "\n4. Verificando Monolog...\n";
if (class_exists('Monolog\Logger')) {
    echo "   ✅ Monolog\\Logger se puede cargar correctamente\n";
    
    // Intentar crear una instancia (sin inicializarla completamente)
    try {
        $reflection = new ReflectionClass('Monolog\Logger');
        $interfaces = $reflection->getInterfaceNames();
        echo "   ✅ Monolog implementa: " . implode(', ', $interfaces) . "\n";
    } catch (Exception $e) {
        echo "   ⚠️  No se pudo inspeccionar Monolog: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ❌ Monolog\\Logger no se puede cargar\n";
    exit(1);
}

echo "\n✅✅✅ FIX EXITOSO ✅✅✅\n";
echo "\nAhora puedes ejecutar:\n";
echo "  php artisan make:filament-user --panel=admin\n\n";

