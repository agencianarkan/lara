<?php
/**
 * Fix FINAL para Monolog - modifica directamente para evitar conflicto con PHP 8.4
 */

$monologPath = __DIR__ . '/../vendor/monolog/monolog/src/Monolog/Logger.php';

if (!file_exists($monologPath)) {
    die("❌ No se encontró Monolog\n");
}

echo "🔧 Aplicando fix FINAL para Monolog...\n";

$content = file_get_contents($monologPath);
$originalContent = $content;

// Verificar el marcador
if (strpos($content, 'FIXED-FINAL-PHP84') !== false) {
    echo "ℹ️  El fix final ya está aplicado\n";
    
    // Verificar que realmente esté bien
    if (preg_match('/class\s+Logger\s+.*?implements\s+\\\?Psr\\\Log\\\LoggerInterface/', $content)) {
        echo "✅ La declaración parece correcta\n";
        return;
    }
}

// Estrategia: Buscar la línea exacta de declaración y modificarla
$lines = explode("\n", $content);
$modified = false;
$newLines = [];
$classLineFound = false;

foreach ($lines as $i => $line) {
    $lineNum = $i + 1;
    
    // Buscar la línea que declara la clase
    if (preg_match('/^\s*class\s+Logger\s+(?:extends\s+\S+\s+)?implements\s+(.+)$/', $line, $matches)) {
        $classLineFound = true;
        $implementsPart = $matches[1];
        
        echo "   Línea $lineNum encontrada: class Logger implements...\n";
        echo "   Parte implements actual: $implementsPart\n";
        
        // Extraer todas las interfaces
        // Puede ser: LoggerInterface, ResettableInterface
        // O: \Psr\Log\LoggerInterface, ResettableInterface
        // O: Psr\Log\LoggerInterface (con use statement)
        
        // Reemplazar LoggerInterface (sin namespace o con namespace relativo) 
        // por \Psr\Log\LoggerInterface (namespace absoluto)
        $newImplements = preg_replace(
            '/(?:^|\s|,)(?<!\\\\)(?:Psr\\\Log\\\\)?LoggerInterface(?!\\\\)/',
            ' \\Psr\\Log\\LoggerInterface',
            ' ' . $implementsPart
        );
        
        // Limpiar espacios múltiples
        $newImplements = preg_replace('/\s+/', ' ', trim($newImplements));
        
        // Reconstruir la línea
        $newLine = preg_replace(
            '/implements\s+.+$/',
            'implements ' . $newImplements . ' // FIXED-FINAL-PHP84: Force absolute namespace',
            $line
        );
        
        if ($newLine !== $line) {
            echo "   Modificando a: " . trim($newLine) . "\n";
            $modified = true;
        }
        
        $newLines[] = $newLine;
    } else {
        $newLines[] = $line;
    }
}

if (!$classLineFound) {
    echo "⚠️  No se encontró la línea de declaración de la clase\n";
    echo "   Buscando manualmente...\n";
    
    // Buscar de otra manera
    foreach ($lines as $i => $line) {
        if (strpos($line, 'class Logger') !== false) {
            echo "   Línea " . ($i + 1) . ": " . trim($line) . "\n";
        }
    }
}

if ($modified) {
    // Backup
    $backupPath = $monologPath . '.backup-final.' . date('YmdHis');
    copy($monologPath, $backupPath);
    echo "   ✅ Backup: " . basename($backupPath) . "\n";
    
    // Escribir
    file_put_contents($monologPath, implode("\n", $newLines));
    echo "✅ Fix final aplicado\n";
} else {
    if ($classLineFound) {
        echo "ℹ️  La línea ya parece estar correcta\n";
    } else {
        echo "❌ No se pudo encontrar la línea para modificar\n";
    }
}

