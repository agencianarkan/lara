<?php
/**
 * Fix RADICAL para Monolog - elimina el use statement y usa solo namespace absoluto
 */

$monologPath = __DIR__ . '/../vendor/monolog/monolog/src/Monolog/Logger.php';

if (!file_exists($monologPath)) {
    die("❌ No se encontró Monolog\n");
}

echo "🔧 Aplicando fix RADICAL para Monolog...\n";

$content = file_get_contents($monologPath);

// Verificar si ya está corregido
if (strpos($content, '// FIXED-RADICAL-PHP84') !== false) {
    echo "ℹ️  El fix radical ya está aplicado\n";
    return;
}

$lines = explode("\n", $content);
$modified = false;
$newLines = [];

foreach ($lines as $i => $line) {
    $lineNum = $i + 1;
    
    // Paso 1: Eliminar o comentar el use statement de LoggerInterface
    // Buscar: use \Psr\Log\LoggerInterface; o use Psr\Log\LoggerInterface;
    if (preg_match('/^\s*use\s+(?:\\\?)?Psr\\\Log\\\LoggerInterface\s*;/', $line)) {
        echo "   Línea $lineNum: Eliminando use statement\n";
        // Comentar en lugar de eliminar por si acaso
        $newLines[] = '// ' . trim($line) . ' // FIXED-RADICAL-PHP84: Removed to force absolute namespace';
        $modified = true;
        continue;
    }
    
    // Paso 2: Asegurar que la declaración de la clase use SOLO namespace absoluto
    if (preg_match('/^\s*class\s+Logger\s+(?:extends\s+\S+\s+)?implements\s+/', $line)) {
        // Reemplazar cualquier referencia a LoggerInterface sin backslash inicial
        $newLine = preg_replace(
            '/(implements\s+)(?<!\\\\)Psr\\\Log\\\LoggerInterface/',
            '$1\\Psr\\Log\\LoggerInterface',
            $line
        );
        
        // Asegurar que tenga el comentario de fix
        if (strpos($newLine, 'FIXED-RADICAL-PHP84') === false) {
            $newLine = rtrim($newLine) . ' // FIXED-RADICAL-PHP84: Force absolute namespace only';
        }
        
        if ($newLine !== $line) {
            echo "   Línea $lineNum: Asegurando namespace absoluto\n";
            $modified = true;
        }
        
        $newLines[] = $newLine;
        continue;
    }
    
    // Paso 3: En todas las referencias a LoggerInterface en el código, usar \Psr\Log\LoggerInterface
    // Esto incluye type hints, instanceof, etc.
    // Pero solo si no es un comentario
    if (strpos(trim($line), '//') !== 0 && strpos(trim($line), '*') !== 0) {
        // Buscar referencias a LoggerInterface que no tengan el backslash inicial
        $newLine = preg_replace(
            '/(?<!\\\\)(?:^|\s)(Psr\\\Log\\\LoggerInterface)(?!\\\\)/',
            ' \\Psr\\Log\\LoggerInterface',
            $line
        );
        
        if ($newLine !== $line) {
            echo "   Línea $lineNum: Corrigiendo referencia a LoggerInterface\n";
            $modified = true;
            $newLines[] = $newLine;
            continue;
        }
    }
    
    $newLines[] = $line;
}

if ($modified) {
    // Backup
    $backupPath = $monologPath . '.backup-radical.' . date('YmdHis');
    copy($monologPath, $backupPath);
    echo "   ✅ Backup: " . basename($backupPath) . "\n";
    
    // Escribir
    file_put_contents($monologPath, implode("\n", $newLines));
    echo "✅ Fix radical aplicado\n";
    
    echo "\n⚠️  IMPORTANTE: Si esto no funciona, la única solución permanente es:\n";
    echo "   1. Contactar al hosting para deshabilitar la extensión psr en PHP 8.4\n";
    echo "   2. O cambiar a PHP 8.3 o 8.2\n";
} else {
    echo "ℹ️  No se necesitaron cambios\n";
}

