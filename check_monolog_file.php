<?php
/**
 * Script para verificar exactamente cómo está declarada la clase Logger
 */

$monologPath = __DIR__ . '/vendor/monolog/monolog/src/Monolog/Logger.php';

if (!file_exists($monologPath)) {
    die("❌ No se encontró Monolog\n");
}

echo "=== VERIFICACIÓN DETALLADA DE MONOLOG ===\n\n";

$content = file_get_contents($monologPath);
$lines = explode("\n", $content);

// Buscar la línea de declaración de la clase
echo "1. Buscando declaración de la clase Logger:\n";
$classLineNum = null;
$classLine = null;

foreach ($lines as $i => $line) {
    $lineNum = $i + 1;
    if (preg_match('/^\s*class\s+Logger/', $line)) {
        $classLineNum = $lineNum;
        $classLine = $line;
        echo "   Línea $lineNum: " . trim($line) . "\n";
        break;
    }
}

if (!$classLine) {
    die("❌ No se encontró la declaración de la clase\n");
}

// Verificar qué implementa
echo "\n2. Análisis de la declaración:\n";

// Extraer la parte de implements
if (preg_match('/implements\s+(.+?)(?:\s*\{|\s*\/\/|\s*$)/', $classLine, $matches)) {
    $implements = trim($matches[1]);
    echo "   Parte 'implements': $implements\n";
    
    // Separar por comas
    $interfaces = array_map('trim', explode(',', $implements));
    echo "   Interfaces encontradas:\n";
    foreach ($interfaces as $iface) {
        // Limpiar comentarios
        $iface = preg_replace('/\/\/.*$/', '', $iface);
        $iface = trim($iface);
        
        if (strpos($iface, 'Psr\Log\LoggerInterface') !== false || 
            strpos($iface, '\Psr\Log\LoggerInterface') !== false) {
            if ($iface === '\Psr\Log\LoggerInterface' || $iface === '\\Psr\\Log\\LoggerInterface') {
                echo "      ✅ $iface (namespace absoluto - CORRECTO)\n";
            } else {
                echo "      ⚠️  $iface (puede tener problema)\n";
            }
        } else {
            echo "      - $iface\n";
        }
    }
} else {
    echo "   ❌ No se pudo extraer la parte 'implements'\n";
}

// Verificar el use statement
echo "\n3. Verificando use statements:\n";
foreach ($lines as $i => $line) {
    if (preg_match('/^\s*use\s+(.+?)\s*;/', $line, $matches)) {
        $use = $matches[1];
        if (strpos($use, 'LoggerInterface') !== false) {
            echo "   Línea " . ($i + 1) . ": use $use;\n";
        }
    }
}

// Mostrar las líneas alrededor de la declaración
echo "\n4. Líneas alrededor de la declaración:\n";
$start = max(0, $classLineNum - 5);
$end = min(count($lines), $classLineNum + 5);
for ($i = $start; $i < $end; $i++) {
    $marker = ($i + 1 == $classLineNum) ? ' >>> ' : '     ';
    echo $marker . ($i + 1) . ': ' . $lines[$i] . "\n";
}

echo "\n=== FIN ===\n";

