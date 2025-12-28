<?php
/**
 * Script alternativo para crear usuario sin usar artisan
 * Ejecutar: php create_user_direct.php
 */

// Aplicar fix de PSR
require_once __DIR__ . '/bootstrap/psr-fix.php';

// Cargar autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Cargar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== CREAR USUARIO ADMINISTRADOR PARA FILAMENT ===\n\n";

// Datos del usuario (MODIFICA ESTOS VALORES)
$name = 'Administrador';
$email = 'admin@lara.narkan.cl'; // CAMBIA ESTE EMAIL
$password = 'Admin123!'; // CAMBIA ESTA CONTRASEÑA

// Verificar si el usuario ya existe
if (User::where('email', $email)->exists()) {
    echo "❌ El usuario con email '{$email}' ya existe.\n";
    echo "   Usa otro email o elimina el usuario existente primero.\n";
    exit(1);
}

try {
    // Crear el usuario
    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password),
        'email_verified_at' => now(),
    ]);

    echo "✅ Usuario creado exitosamente!\n\n";
    echo "Credenciales de acceso:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Email:    {$email}\n";
    echo "Password: {$password}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "🌐 Accede al panel en: https://lara.narkan.cl/admin\n\n";
    echo "⚠️  IMPORTANTE:\n";
    echo "   1. Cambia la contraseña después del primer acceso\n";
    echo "   2. Elimina este archivo por seguridad: create_user_direct.php\n";
    
} catch (Exception $e) {
    echo "❌ ERROR al crear usuario: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}

