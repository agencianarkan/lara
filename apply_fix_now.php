<?php
/**
 * Script para aplicar el fix de Monolog AHORA
 * Ejecuta este script después de subir los archivos al hosting
 */

echo "=== APLICANDO FIX PERMANENTE DE MONOLOG ===\n\n";

// Ejecutar el script de fix
require __DIR__ . '/scripts/fix-monolog-psr.php';

echo "\n✅ Fix aplicado. Ahora prueba acceder a /admin\n";

