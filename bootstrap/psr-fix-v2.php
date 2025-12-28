<?php
/**
 * Fix V2: Solución más agresiva para el conflicto PSR
 * 
 * PROBLEMA: La extensión psr define PsrExt\Log\LoggerInterface y Monolog
 *           la detecta automáticamente, causando un conflicto de firmas.
 * 
 * SOLUCIÓN: Interceptar el autoloader y forzar el uso de la biblioteca psr/log
 */

if (extension_loaded('psr')) {
    // Registrar un autoloader personalizado que tenga prioridad
    // sobre el autoloader de Composer para las clases PSR
    spl_autoload_register(function ($class) {
        // Si es una clase de la extensión PsrExt, ignorarla y usar la biblioteca
        if (strpos($class, 'PsrExt\\') === 0) {
            // Convertir PsrExt\Log\LoggerInterface a Psr\Log\LoggerInterface
            $libraryClass = str_replace('PsrExt\\', 'Psr\\', $class);
            
            // Si la clase de la biblioteca existe, usarla
            if (class_exists($libraryClass) || interface_exists($libraryClass)) {
                // Crear un alias para que las referencias a PsrExt usen Psr
                if (!class_exists($class, false) && !interface_exists($class, false)) {
                    class_alias($libraryClass, $class);
                }
                return true;
            }
        }
        return false;
    }, true, true); // true, true = prepend, throw
    
    // Cargar manualmente las interfaces de psr/log para asegurar que existan
    $psrLogPath = __DIR__ . '/../vendor/psr/log/src';
    if (is_dir($psrLogPath)) {
        $files = [
            'LogLevel.php',
            'LoggerInterface.php',
            'LoggerAwareInterface.php',
        ];
        
        foreach ($files as $file) {
            $fullPath = $psrLogPath . '/' . $file;
            if (file_exists($fullPath)) {
                $className = 'Psr\\Log\\' . pathinfo($file, PATHINFO_FILENAME);
                if (!class_exists($className, false) && !interface_exists($className, false)) {
                    require_once $fullPath;
                }
            }
        }
    }
}

