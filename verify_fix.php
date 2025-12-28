<?php
/**
 * Script para verificar si el fix se aplicó correctamente
 */

echo "=== VERIFICACIÓN DEL FIX DE MONOLOG ===\n\n";

$monologPath = __DIR__ . '/vendor/monolog/monolog/src/Monolog/Logger.php';

if (!file_exists($monologPath)) {
    die("❌ No se encontró Monolog en: $monologPath\n");
}

echo "1. Verificando archivo de Monolog...\n";
$content = file_get_contents($monologPath);

// Buscar la línea de declaración de la clase
if (preg_match('/class\s+Logger\s+.*?implements\s+(.+?)\s*(?:\{|\/\/)/s', $content, $matches)) {
    $implements = trim($matches[1]);
    echo "   Línea encontrada: implements $implements\n";
    
    if (strpos($implements, '\\Psr\\Log\\LoggerInterface') !== false || 
        strpos($implements, 'Psr\\Log\\LoggerInterface') !== false) {
        echo "   ✅ CORRECTO: Usa namespace completo\n";
    } else {
        echo "   ❌ INCORRECTO: NO usa namespace completo\n";
        echo "   ⚠️  El fix no se aplicó correctamente\n";
    }
} else {
    echo "   ⚠️  No se pudo encontrar la declaración de la clase\n";
}

// Verificar si tiene el comentario del fix
if (strpos($content, 'FIXED-PERMANENT') !== false) {
    echo "\n2. ✅ El archivo tiene el marcador del fix permanente\n";
} else {
    echo "\n2. ❌ El archivo NO tiene el marcador del fix permanente\n";
    echo "   El fix no se ha aplicado todavía\n";
}

// Mostrar las primeras líneas relevantes
echo "\n3. Primeras líneas relevantes del archivo:\n";
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    $lineNum = $i + 1;
    if (preg_match('/class\s+Logger|implements|namespace|use.*LoggerInterface/', $line)) {
        echo "   Línea $lineNum: " . trim($line) . "\n";
        if ($i > 200) break; // Limitar búsqueda
    }
}

echo "\n4. Aplicando fix ahora...\n";
require __DIR__ . '/scripts/fix-monolog-psr.php';

echo "\n5. Verificando nuevamente después del fix...\n";
$content = file_get_contents($monologPath);
if (strpos($content, 'FIXED-PERMANENT') !== false) {
    echo "   ✅ Fix aplicado correctamente\n";
} else {
    echo "   ❌ El fix aún no se aplicó\n";
}

