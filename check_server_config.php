<?php
/**
 * Script para verificar la configuración del servidor web
 */

echo "=== VERIFICACIÓN DE CONFIGURACIÓN DEL SERVIDOR ===\n\n";

// Verificar si estamos en modo web o CLI
$sapi = php_sapi_name();
echo "1. Entorno PHP: $sapi\n";

if ($sapi === 'cli') {
    echo "   ⚠️  Ejecutándose en CLI, no podemos verificar el servidor web directamente\n";
    echo "   ✅ Ejecuta este script desde el navegador: https://lara.narkan.cl/check_server_config.php\n";
} else {
    echo "   ✅ Ejecutándose en modo web\n\n";
    
    // Verificar DOCUMENT_ROOT
    echo "2. DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'no definido') . "\n";
    echo "   Script actual: " . __FILE__ . "\n";
    
    // Verificar si index.php existe en la raíz documentada
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $indexInDocRoot = $docRoot . '/index.php';
    echo "   index.php en DOCUMENT_ROOT: " . (file_exists($indexInDocRoot) ? '✅ Existe' : '❌ No existe') . "\n";
    
    // Verificar si estamos en public o en raíz
    $currentDir = dirname($_SERVER['SCRIPT_FILENAME']);
    $isInPublic = strpos($currentDir, '/public') !== false || strpos($currentDir, '\\public') !== false;
    echo "   Estamos en carpeta public: " . ($isInPublic ? '✅ Sí' : '❌ No') . "\n\n";
    
    // Verificar .htaccess
    echo "3. Verificando .htaccess:\n";
    $htaccessPath = dirname($_SERVER['SCRIPT_FILENAME']) . '/.htaccess';
    if (file_exists($htaccessPath)) {
        echo "   ✅ .htaccess existe en: $htaccessPath\n";
        $htaccess = file_get_contents($htaccessPath);
        if (strpos($htaccess, 'RewriteEngine') !== false) {
            echo "   ✅ RewriteEngine está habilitado\n";
        } else {
            echo "   ❌ RewriteEngine NO está configurado\n";
        }
    } else {
        echo "   ❌ .htaccess NO existe\n";
    }
    
    // Verificar mod_rewrite
    echo "\n4. Verificando módulos de Apache:\n";
    if (function_exists('apache_get_modules')) {
        $modules = apache_get_modules();
        if (in_array('mod_rewrite', $modules)) {
            echo "   ✅ mod_rewrite está cargado\n";
        } else {
            echo "   ❌ mod_rewrite NO está cargado\n";
        }
    } else {
        echo "   ⚠️  No se puede verificar (función no disponible)\n";
    }
    
    // Verificar acceso a /admin
    echo "\n5. Test de acceso a /admin:\n";
    $adminUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/admin';
    echo "   URL del panel: $adminUrl\n";
    
    // Intentar hacer una petición interna
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'User-Agent: PHP Server Check',
            'timeout' => 5,
        ]
    ]);
    
    $result = @file_get_contents($adminUrl, false, $context);
    if ($result !== false) {
        echo "   ✅ La URL responde (no es 404)\n";
    } else {
        $error = error_get_last();
        echo "   ❌ Error al acceder: " . ($error['message'] ?? 'Desconocido') . "\n";
    }
}

echo "\n=== RECOMENDACIONES ===\n";
echo "Si DOCUMENT_ROOT no apunta a /public:\n";
echo "1. Contacta a tu hosting para configurar el dominio\n";
echo "2. O crea un .htaccess en la raíz que redirija a /public\n";
echo "\nSi las rutas no funcionan:\n";
echo "1. Verifica que mod_rewrite esté habilitado\n";
echo "2. Verifica que .htaccess esté en la carpeta public\n";

