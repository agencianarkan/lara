<?php
/**
 * Script permanente para corregir el conflicto entre PHP 8.4 PSR extension y Monolog
 * Este script se ejecuta automáticamente después de composer install/update
 */

$monologPath = __DIR__ . '/../vendor/monolog/monolog/src/Monolog/Logger.php';

if (!file_exists($monologPath)) {
    // Monolog no está instalado aún, no hay nada que hacer
    return;
}

// Leer el archivo
$content = file_get_contents($monologPath);

// Verificar si ya está corregido
if (strpos($content, '// FIXED-PERMANENT: Forced Psr\\Log\\LoggerInterface') !== false) {
    // Ya está corregido, no hacer nada
    return;
}

echo "🔧 Corrigiendo conflicto PSR/Monolog en PHP 8.4...\n";

// Buscar todas las declaraciones de "implements LoggerInterface" o "extends LoggerInterface"
// y cambiarlas a usar el namespace completo \Psr\Log\LoggerInterface

// Buscar la línea que declara la clase: "class Logger implements LoggerInterface"
// Necesitamos cambiar "implements LoggerInterface" a "implements \Psr\Log\LoggerInterface"

// Patrón más simple: busca "implements LoggerInterface" sin namespace
$pattern = '/(implements\s+)(LoggerInterface)(\s|;|$)/';
$replacement = '$1\\Psr\\Log\\LoggerInterface // FIXED-PERMANENT: Forced Psr\\Log\\LoggerInterface for PHP 8.4$3';

$newContent = $content;
$changes = 0;

// Aplicar corrección solo una vez (para la declaración de la clase)
$newContent = preg_replace($pattern, $replacement, $newContent, 1, $count);
$changes += $count;

// También necesitamos asegurarnos de que el "use" statement esté correcto
// Buscar: use Psr\Log\LoggerInterface;
if (strpos($newContent, 'use Psr\Log\LoggerInterface;') === false && 
    strpos($newContent, 'use \Psr\Log\LoggerInterface;') === false) {
    // Agregar el use statement después de otros use statements
    $usePattern = '/(use\s+[^;]+;\s*\n)/';
    $useReplacement = '$1use \Psr\Log\LoggerInterface; // FIXED-PERMANENT: Added for PHP 8.4 compatibility' . "\n";
    $newContent = preg_replace($usePattern, $useReplacement, $newContent, 1);
    $changes++;
}

if ($changes > 0) {
    // Hacer backup solo si no existe uno reciente (menos de 1 hora)
    $backupPath = $monologPath . '.backup';
    if (!file_exists($backupPath) || (time() - filemtime($backupPath)) > 3600) {
        copy($monologPath, $backupPath);
    }
    
    // Escribir el archivo corregido
    file_put_contents($monologPath, $newContent);
    echo "✅ Monolog corregido ($changes cambios aplicados)\n";
} else {
    echo "ℹ️  Monolog ya estaba correcto o no se necesitaron cambios\n";
}

