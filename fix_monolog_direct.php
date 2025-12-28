<?php
/**
 * Script para modificar directamente Monolog y forzar el uso de Psr\Log\LoggerInterface
 * 
 * ⚠️ ADVERTENCIA: Esto modifica archivos en vendor/, que se sobrescribirán con composer update
 * ⚠️ Úsalo solo como solución temporal mientras contactas al hosting
 */

$monologPath = __DIR__ . '/vendor/monolog/monolog/src/Monolog/Logger.php';

if (!file_exists($monologPath)) {
    die("❌ No se encontró Monolog en: $monologPath\n");
}

echo "=== FIX DIRECTO DE MONOLOG ===\n\n";

// Leer el archivo
$content = file_get_contents($monologPath);

// Verificar si ya está modificado
if (strpos($content, '// FIXED: Forced Psr\\Log\\LoggerInterface') !== false) {
    echo "✅ El archivo ya está modificado.\n";
    exit(0);
}

// Buscar la declaración de la clase
// Monolog usa: class Logger implements LoggerInterface
// Necesitamos cambiarla a: class Logger implements \Psr\Log\LoggerInterface

$original = '/implements\s+LoggerInterface/';
$replacement = 'implements \\Psr\\Log\\LoggerInterface // FIXED: Forced Psr\\Log\\LoggerInterface';

$newContent = preg_replace($original, $replacement, $content, 1, $count);

if ($count === 0) {
    die("❌ No se encontró la declaración 'implements LoggerInterface' en el archivo.\n");
}

// Hacer backup
$backupPath = $monologPath . '.backup.' . date('YmdHis');
copy($monologPath, $backupPath);
echo "✅ Backup creado: " . basename($backupPath) . "\n";

// Escribir el archivo modificado
file_put_contents($monologPath, $newContent);
echo "✅ Archivo modificado exitosamente.\n\n";

echo "⚠️  IMPORTANTE:\n";
echo "   - Este cambio se perderá si ejecutas 'composer update'\n";
echo "   - Es una solución TEMPORAL\n";
echo "   - Contacta a tu hosting para deshabilitar la extensión psr o cambiar a PHP 8.3\n\n";

echo "✅ Ahora prueba acceder a /admin\n";

