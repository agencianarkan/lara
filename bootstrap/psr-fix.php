<?php
/**
 * Fix para el conflicto entre extensión psr (PHP 8.4) y Monolog
 * 
 * PROBLEMA: PHP 8.4 incluye una extensión nativa 'psr' que define PsrExt\Log\LoggerInterface
 *           pero Monolog espera implementar Psr\Log\LoggerInterface de la biblioteca psr/log.
 *           Cuando Monolog intenta implementar la interfaz, encuentra que la extensión
 *           ya la definió con una firma diferente (sin tipo Stringable).
 * 
 * SOLUCIÓN: Cargar manualmente las clases de psr/log ANTES del autoloader para que
 *           las interfaces de la biblioteca tengan prioridad sobre las de la extensión.
 */

if (extension_loaded('psr')) {
    $psrLogPath = __DIR__ . '/../vendor/psr/log/src';
    
    if (is_dir($psrLogPath)) {
        // Cargar las interfaces críticas de psr/log manualmente
        // Esto asegura que las interfaces de la biblioteca se carguen antes
        // de que el autoloader intente usar las de la extensión
        
        $files = [
            'LogLevel.php',
            'LoggerInterface.php',
            'LoggerAwareInterface.php',
        ];
        
        foreach ($files as $file) {
            $fullPath = $psrLogPath . '/' . $file;
            if (file_exists($fullPath) && !class_exists('Psr\Log\\' . pathinfo($file, PATHINFO_FILENAME), false) 
                && !interface_exists('Psr\Log\\' . pathinfo($file, PATHINFO_FILENAME), false)) {
                require_once $fullPath;
            }
        }
    }
}

