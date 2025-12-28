# Instalación de Filament PHP

## Pasos para completar la instalación en el hosting

### 1. Subir los cambios a GitHub
Primero, asegúrate de hacer commit y push de todos los cambios a GitHub Desktop para que se sincronicen con el hosting.

### 2. Ejecutar en la terminal del hosting

Conéctate a la terminal de tu hosting y ejecuta los siguientes comandos en orden:

#### a) Navegar al directorio del proyecto
```bash
cd /home/tekeclil/domains/lara.narkan.cl/public_html
```

#### b) Instalar las dependencias de Composer
```bash
composer install --no-dev --optimize-autoloader
```

#### c) Publicar los assets de Filament
```bash
php artisan filament:install --panels
```

#### d) Publicar los assets de Filament (CSS/JS)
```bash
php artisan filament:assets
```

#### e) Limpiar y optimizar el caché
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### f) Ejecutar las migraciones (si aún no lo has hecho)
```bash
php artisan migrate
```

#### g) Crear un usuario administrador
```bash
php artisan make:filament-user
```
Este comando te pedirá:
- Nombre
- Email
- Contraseña

### 3. Acceder al panel de administración

Una vez completados los pasos anteriores, podrás acceder a Filament en:

```
https://lara.narkan.cl/admin
```

### 4. Características incluidas

- ✅ Panel de administración en `/admin`
- ✅ Gestión de usuarios (crear, editar, eliminar)
- ✅ Autenticación integrada
- ✅ Interfaz en español
- ✅ Tabla de usuarios con búsqueda y ordenamiento

### Notas importantes

- Si tienes problemas de permisos, asegúrate de que las carpetas `storage` y `bootstrap/cache` tengan permisos de escritura
- Si después de instalar no ves el panel, verifica que el archivo `.env` tenga `APP_URL` configurado correctamente
- Para crear más recursos (tablas de base de datos), puedes usar: `php artisan make:filament-resource NombreModelo`

