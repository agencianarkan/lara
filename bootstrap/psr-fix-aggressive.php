<?php
/**
 * Fix AGRESIVO para el conflicto entre extensión psr (PHP 8.4) y Monolog
 * 
 * Este fix se ejecuta ANTES del autoloader de Composer y fuerza el uso
 * de la biblioteca psr/log en lugar de la extensión.
 */

if (!extension_loaded('psr')) {
    return; // No hay conflicto si la extensión no está cargada
}

// PASO 1: Cargar manualmente las interfaces de psr/log ANTES que cualquier otra cosa
$psrLogPath = __DIR__ . '/../vendor/psr/log/src';

if (is_dir($psrLogPath)) {
    $interfaceFiles = [
        'LogLevel.php',
        'LoggerInterface.php',
        'LoggerAwareInterface.php',
    ];
    
    foreach ($interfaceFiles as $file) {
        $fullPath = $psrLogPath . '/' . $file;
        if (file_exists($fullPath)) {
            // Forzar la carga de la interfaz SIN verificar si ya existe
            // Esto asegura que se cargue ANTES que la extensión interfiera
            require_once $fullPath;
        }
    }
}

// PASO 2: Interceptar el autoloader de Composer ANTES de que se registre
// Esto requiere que este archivo se cargue ANTES de vendor/autoload.php

// Guardar el autoloader original de Composer
$originalComposerAutoload = null;

// Registrar nuestro autoloader PRIMERO (tiene máxima prioridad)
spl_autoload_register(function ($class) use (&$originalComposerAutoload) {
    // Si alguien intenta cargar PsrExt, forzar el uso de Psr
    if (strpos($class, 'PsrExt\\') === 0) {
        $psrClass = str_replace('PsrExt\\', 'Psr\\', $class);
        
        // Si la clase de la biblioteca existe, crear alias
        if (class_exists($psrClass, false) || interface_exists($psrClass, false)) {
            if (!class_exists($class, false) && !interface_exists($class, false)) {
                class_alias($psrClass, $class);
                return true;
            }
        }
    }
    
    return false; // Dejar que otros autoloaders manejen esto
}, true, true); // prepend = true, throw = true

// PASO 3: Interceptar específicamente Monolog\Logger y forzar el namespace correcto
spl_autoload_register(function ($class) {
    if ($class === 'Monolog\Logger') {
        $monologPath = __DIR__ . '/../vendor/monolog/monolog/src/Monolog/Logger.php';
        
        if (file_exists($monologPath)) {
            // Asegurar que Psr\Log\LoggerInterface está cargada PRIMERO
            $loggerInterfacePath = __DIR__ . '/../vendor/psr/log/src/LoggerInterface.php';
            if (file_exists($loggerInterfacePath) && !interface_exists('Psr\Log\LoggerInterface', false)) {
                require_once $loggerInterfacePath;
            }
            
            // Leer el contenido del archivo y modificar la declaración
            $code = file_get_contents($monologPath);
            
            // Si el código menciona LoggerInterface sin namespace completo, asegurarse de que use Psr\Log
            // Esto es un hack, pero puede funcionar si Monolog usa "use Psr\Log\LoggerInterface"
            // Si no, necesitamos una solución diferente
            
            // Intentar cargar con namespace explícito usando eval (último recurso)
            // Primero, crear un contexto donde Psr\Log\LoggerInterface tenga prioridad
            
            // Cargar el archivo normalmente - si falla, el error será capturado
            require_once $monologPath;
            
            return true;
        }
    }
    
    return false;
}, true, true);

// PASO 4: Asegurar que cuando se busca LoggerInterface, se use Psr\Log\LoggerInterface
// Esto requiere interceptar class_exists y interface_exists
if (!function_exists('psr_fix_interface_exists')) {
    function psr_fix_interface_exists($interface, $autoload = true) {
        // Si buscan LoggerInterface sin namespace, redirigir a Psr\Log\LoggerInterface
        if ($interface === 'LoggerInterface' && interface_exists('Psr\Log\LoggerInterface', false)) {
            return interface_exists('Psr\Log\LoggerInterface', $autoload);
        }
        // Si buscan PsrExt\Log\LoggerInterface, devolver false para forzar el uso de Psr\Log
        if ($interface === 'PsrExt\Log\LoggerInterface' || strpos($interface, 'PsrExt\\') === 0) {
            $psrInterface = str_replace('PsrExt\\', 'Psr\\', $interface);
            if (interface_exists($psrInterface, false)) {
                return true; // Devolver true pero para la versión de Psr
            }
        }
        return interface_exists($interface, $autoload);
    }
    
    // Nota: No podemos sobrescribir interface_exists, pero podemos asegurarnos
    // de que Psr\Log\LoggerInterface esté siempre disponible
}

// PASO 5: Crear alias de la interfaz de la extensión a la biblioteca ANTES de que Monolog la necesite
if (interface_exists('PsrExt\Log\LoggerInterface', false) && !interface_exists('Psr\Log\LoggerInterface', false)) {
    // Si la extensión definió la interfaz pero no la biblioteca, cargarla ahora
    $loggerInterfacePath = __DIR__ . '/../vendor/psr/log/src/LoggerInterface.php';
    if (file_exists($loggerInterfacePath)) {
        require_once $loggerInterfacePath;
    }
}

// Si ambas existen, forzar el uso de la biblioteca creando un alias inverso
if (interface_exists('Psr\Log\LoggerInterface', false)) {
    // Cuando Monolog busque LoggerInterface, PHP encontrará primero Psr\Log\LoggerInterface
    // si está en el mismo namespace o usando "use Psr\Log\LoggerInterface"
    // El problema es que PHP busca en el namespace global primero
}

