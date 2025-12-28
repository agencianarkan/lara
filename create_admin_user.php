<?php

/**
 * Script temporal para crear un usuario administrador
 * Ejecutar: php create_admin_user.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Creando usuario administrador para Filament...\n\n";

// Datos del usuario (modifica estos valores si lo deseas)
$name = 'Administrador';
$email = 'admin@example.com';
$password = 'password123'; // Cambia esto por una contraseña segura

// Verificar si el usuario ya existe
if (User::where('email', $email)->exists()) {
    echo "El usuario con email '{$email}' ya existe.\n";
    exit(1);
}

// Crear el usuario
$user = User::create([
    'name' => $name,
    'email' => $email,
    'password' => Hash::make($password),
    'email_verified_at' => now(),
]);

echo "✅ Usuario creado exitosamente!\n\n";
echo "Credenciales de acceso:\n";
echo "Email: {$email}\n";
echo "Contraseña: {$password}\n\n";
echo "Accede al panel en: https://lara.narkan.cl/admin\n";
echo "\n⚠️  IMPORTANTE: Cambia la contraseña después del primer acceso y elimina este archivo.\n";

