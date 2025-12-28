<?php
/**
 * Script para verificar la configuración del servidor web
 * Colocar este archivo en public/ para verificar desde el navegador
 */

echo "=== VERIFICACIÓN DE CONFIGURACIÓN DEL SERVIDOR ===\n\n";

// Verificar DOCUMENT_ROOT
echo "1. DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'no definido') . "\n";
echo "   Script actual: " . __FILE__ . "\n";

// Verificar si index.php de Laravel existe
$laravelIndex = dirname(__DIR__) . '/public/index.php';
echo "   Laravel index.php: " . (file_exists($laravelIndex) ? '✅ Existe' : '❌ No existe') . "\n\n";

// Verificar .htaccess
echo "2. Verificando .htaccess:\n";
$htaccessPath = __DIR__ . '/.htaccess';
if (file_exists($htaccessPath)) {
    echo "   ✅ .htaccess existe\n";
    $htaccess = file_get_contents($htaccessPath);
    if (strpos($htaccess, 'RewriteEngine On') !== false) {
        echo "   ✅ RewriteEngine está habilitado\n";
    }
    if (strpos($htaccess, 'index.php') !== false) {
        echo "   ✅ Redirección a index.php configurada\n";
    }
} else {
    echo "   ❌ .htaccess NO existe en public/\n";
}

// Verificar acceso a Laravel
echo "\n3. Test de acceso:\n";
echo "   Intenta acceder a: " . (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . "/admin\n";

// Verificar variables de entorno
echo "\n4. Verificando configuración:\n";
if (file_exists(dirname(__DIR__) . '/.env')) {
    echo "   ✅ Archivo .env existe\n";
} else {
    echo "   ❌ Archivo .env NO existe\n";
}

echo "\n=== CONCLUSIÓN ===\n";
echo "Si ves este mensaje, el servidor web está funcionando.\n";
echo "El problema del 404 en /admin puede ser:\n";
echo "1. Las rutas no están registradas (pero el diagnóstico dice que sí)\n";
echo "2. El servidor web no está procesando .htaccess correctamente\n";
echo "3. Falta compilar los assets de Filament\n";

