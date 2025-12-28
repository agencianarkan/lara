<?php
/**
 * Fix COMPLETO para Monolog - modifica la declaración de la clase directamente
 */

$monologPath = __DIR__ . '/../vendor/monolog/monolog/src/Monolog/Logger.php';

if (!file_exists($monologPath)) {
    die("❌ No se encontró Monolog\n");
}

$content = file_get_contents($monologPath);

// Verificar si ya está completamente corregido
if (strpos($content, '// FIXED-COMPLETE-PHP84') !== false) {
    echo "ℹ️  Monolog ya está completamente corregido\n";
    return;
}

echo "🔧 Aplicando fix COMPLETO para PHP 8.4...\n";

// Leer el archivo línea por línea para encontrar la declaración exacta
$lines = explode("\n", $content);
$modified = false;
$newLines = [];

foreach ($lines as $i => $line) {
    $lineNum = $i + 1;
    
    // Buscar la línea que declara la clase Logger
    // Puede ser: "class Logger implements LoggerInterface" o similar
    if (preg_match('/^\s*class\s+Logger\s+.*?implements\s+/', $line)) {
        // Esta es la línea de declaración de la clase
        // Asegurarse de que use SOLO \Psr\Log\LoggerInterface
        
        // Reemplazar cualquier referencia a LoggerInterface sin namespace
        // o que no sea \Psr\Log\LoggerInterface
        $newLine = preg_replace(
            '/(implements\s+)(?!(?:\\\?Psr\\\Log\\\)|\\\\)(LoggerInterface\b)/',
            '$1\\Psr\\Log\\LoggerInterface',
            $line
        );
        
        // Si la línea tiene múltiples interfaces separadas por comas
        // Asegurarse de que LoggerInterface sea \Psr\Log\LoggerInterface
        $newLine = preg_replace(
            '/(,|\s+)(?!(?:\\\?Psr\\\Log\\\)|\\\\)(LoggerInterface\b)/',
            '$1\\Psr\\Log\\LoggerInterface',
            $newLine
        );
        
        // Agregar comentario de fix
        if (strpos($newLine, '// FIXED-COMPLETE-PHP84') === false) {
            $newLine = preg_replace(
                '/(\s*)(\})/',
                '$1// FIXED-COMPLETE-PHP84: Force Psr\\Log namespace to avoid PHP 8.4 extension conflict$1$2',
                $newLine,
                1
            );
            
            // Si no encontró el patrón, agregar el comentario al final de la línea
            if (strpos($newLine, 'FIXED-COMPLETE-PHP84') === false) {
                $newLine = rtrim($newLine) . ' // FIXED-COMPLETE-PHP84: Force Psr\\Log namespace';
            }
        }
        
        if ($newLine !== $line) {
            echo "   Modificando línea $lineNum\n";
            $modified = true;
        }
        
        $newLines[] = $newLine;
    } else {
        $newLines[] = $line;
    }
}

if ($modified) {
    // Backup
    $backupPath = $monologPath . '.backup-complete.' . date('YmdHis');
    copy($monologPath, $backupPath);
    echo "   ✅ Backup creado: " . basename($backupPath) . "\n";
    
    // Escribir
    file_put_contents($monologPath, implode("\n", $newLines));
    echo "✅ Fix completo aplicado\n";
} else {
    echo "ℹ️  No se necesitaron cambios\n";
    
    // Verificar manualmente la línea de declaración
    foreach ($lines as $i => $line) {
        if (preg_match('/^\s*class\s+Logger/', $line)) {
            echo "   Línea " . ($i + 1) . " encontrada: " . trim($line) . "\n";
            break;
        }
    }
}

