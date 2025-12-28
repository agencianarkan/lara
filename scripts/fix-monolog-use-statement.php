<?php
/**
 * Script para corregir también el "use" statement en Monolog
 */

$monologPath = __DIR__ . '/../vendor/monolog/monolog/src/Monolog/Logger.php';

if (!file_exists($monologPath)) {
    die("❌ No se encontró Monolog\n");
}

$content = file_get_contents($monologPath);

// Verificar si ya está corregido
if (strpos($content, '// FIXED-USE-STATEMENT') !== false) {
    echo "ℹ️  El use statement ya está corregido\n";
    return;
}

echo "🔧 Corrigiendo use statement en Monolog...\n";

// Buscar el use statement de LoggerInterface y asegurarse de que use el namespace completo
// Patrón: use Psr\Log\LoggerInterface;
$pattern = '/(use\s+)Psr\\\Log\\\LoggerInterface(\s*;)/';
$replacement = '$1\\Psr\\Log\\LoggerInterface$2 // FIXED-USE-STATEMENT: Force absolute namespace';

$newContent = preg_replace($pattern, $replacement, $content, -1, $count);

if ($count > 0) {
    // Backup
    $backupPath = $monologPath . '.backup-use';
    copy($monologPath, $backupPath);
    
    file_put_contents($monologPath, $newContent);
    echo "✅ Use statement corregido ($count cambios)\n";
} else {
    echo "ℹ️  No se encontró use statement para corregir (puede que ya esté correcto)\n";
}

