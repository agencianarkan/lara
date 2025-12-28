<?php
/**
 * Script para crear directorios necesarios y verificar permisos
 */

echo "=== FIX DE PERMISOS Y DIRECTORIOS ===\n\n";

$baseDir = __DIR__;

// Directorios necesarios
$dirs = [
    'storage/logs',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'bootstrap/cache',
];

foreach ($dirs as $dir) {
    $fullPath = $baseDir . '/' . $dir;
    
    if (!is_dir($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "✅ Creado: $dir\n";
        } else {
            echo "❌ No se pudo crear: $dir\n";
        }
    } else {
        echo "✅ Existe: $dir\n";
    }
    
    // Verificar permisos
    if (is_dir($fullPath)) {
        $perms = substr(sprintf('%o', fileperms($fullPath)), -4);
        $writable = is_writable($fullPath);
        
        if ($writable) {
            echo "   ✅ Escribible (permisos: $perms)\n";
        } else {
            echo "   ❌ NO escribible (permisos: $perms)\n";
            echo "   Intenta: chmod -R 755 $fullPath\n";
        }
    }
}

// Crear archivo de log si no existe
$logFile = $baseDir . '/storage/logs/laravel.log';
if (!file_exists($logFile)) {
    if (touch($logFile)) {
        chmod($logFile, 0644);
        echo "\n✅ Archivo de log creado: storage/logs/laravel.log\n";
    } else {
        echo "\n❌ No se pudo crear el archivo de log\n";
    }
} else {
    echo "\n✅ Archivo de log existe\n";
}

echo "\n=== FIN ===\n";

