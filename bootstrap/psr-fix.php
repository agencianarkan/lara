<?php
/**
 * Fix para el conflicto entre extensión psr (PHP 8.4) y Monolog
 * 
 * PROBLEMA: PHP 8.4 incluye una extensión nativa 'psr' que define PsrExt\Log\LoggerInterface.
 *           Monolog detecta automáticamente esta interfaz y trata de implementarla, pero
 *           tiene una firma incompatible (sin tipo Stringable en el parámetro $message).
 * 
 * SOLUCIÓN: Interceptar el autoloader y crear aliases para que PsrExt use Psr de la biblioteca.
 *           También cargar manualmente las interfaces de psr/log antes del autoloader.
 */

if (extension_loaded('psr')) {
    // Cargar manualmente las interfaces de psr/log PRIMERO
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
                // Cargar sin verificar existencia para forzar la carga
                if (!class_exists($className, false) && !interface_exists($className, false)) {
                    require_once $fullPath;
                }
            }
        }
    }
    
    // Registrar autoloader que intercepta PsrExt y lo redirige a Psr
    spl_autoload_register(function ($class) {
        // Si alguien intenta usar PsrExt, redirigir a Psr de la biblioteca
        if (strpos($class, 'PsrExt\\Log\\') === 0) {
            $psrClass = str_replace('PsrExt\\Log\\', 'Psr\\Log\\', $class);
            
            // Si la clase de la biblioteca existe, crear un alias
            if (class_exists($psrClass) || interface_exists($psrClass)) {
                if (!class_exists($class, false) && !interface_exists($class, false)) {
                    class_alias($psrClass, $class);
                }
                return true;
            }
        }
        return false;
    }, true, true); // prepend = true para tener prioridad
}

