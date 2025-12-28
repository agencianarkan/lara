# Documentación del Sistema Plaza

## Índice
1. [Introducción](#introducción)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Estructura de Base de Datos](#estructura-de-base-de-datos)
4. [Gestión del Esquema SQL](#gestión-del-esquema-sql)
5. [Tablas del Sistema](#tablas-del-sistema)
6. [Modelos Eloquent](#modelos-eloquent)
7. [Resources de Filament](#resources-de-filament)
8. [APIs y Frontend](#apis-y-frontend)

---

## Introducción

El Sistema Plaza es una plataforma de gestión multi-tenant diseñada para conectar y administrar múltiples ecommerce (WooCommerce, Shopify, Jumpseller, etc.) a través de un panel centralizado. El sistema permite gestionar usuarios, tiendas, equipos, roles y permisos de forma granular.

### Tecnologías Utilizadas
- **Backend**: Laravel 11.47.0
- **Base de Datos**: MariaDB 10.6.23
- **Panel de Administración**: Filament PHP 3.2
- **PHP**: 8.3.27
- **Frontend**: Vue.js (externo)

---

## Arquitectura del Sistema

### Separación de Responsabilidades

#### 1. **Panel de Administración (Filament) - `/admin`**
- **Usuarios**: Equipo de desarrollo y administradores técnicos
- **Propósito**: Configuración técnica del sistema
- **Gestión de**:
  - Roles del sistema (`plaza_roles`)
  - Capacidades del sistema (`plaza_capabilities`)
  - Definiciones de roles (`plaza_role_definitions`)
  - Auditoría de seguridad (`plaza_auth_audit`)

#### 2. **Frontend Vue.js (Externo)**
- **Usuarios**: Dueños de ecommerce y sus colaboradores
- **Propósito**: Aplicación principal para usuarios finales
- **Gestión de**:
  - Usuarios Plaza (`plaza_users`)
  - Tiendas (`plaza_stores`)
  - Equipos (`plaza_teams`)
  - Membresías (`plaza_memberships`)
  - Asignaciones de equipos (`plaza_membership_teams`)
  - Personalizaciones de permisos (`plaza_custom_overrides`)

#### 3. **Backend Laravel (API)**
- **Propósito**: Proveer endpoints REST/API para el frontend Vue.js
- **Modelos**: Todos los modelos Eloquent están disponibles para consumo via API

---

## Estructura de Base de Datos

### Base de Datos Actual
- **Nombre**: `tekeclil_lara655`
- **Motor**: MariaDB 10.6.23
- **Charset**: utf8mb4
- **Collation**: utf8mb4_unicode_ci

### Prefijo de Tablas
Todas las tablas del sistema Plaza utilizan el prefijo `plaza_` para evitar conflictos con otras tablas de Laravel.

---

## ⚠️ GESTIÓN DEL ESQUEMA SQL - CRÍTICO

### Importante: Actualización del Archivo SQL

**CUALQUIER CAMBIO EN LA ESTRUCTURA DE LA BASE DE DATOS DEBE ACTUALIZARSE INMEDIATAMENTE EN EL ARCHIVO SQL.**

#### Archivo SQL Principal
```
database/schema_plaza.sql
```

#### Proceso Obligatorio al Modificar la Base de Datos

1. **Si creas/modificas una migración:**
   - Ejecuta la migración: `php artisan migrate`
   - Exporta el esquema actualizado
   - Actualiza `database/schema_plaza.sql` con el nuevo esquema

2. **Si modificas una tabla directamente en la base de datos:**
   - ⚠️ **NO se recomienda** modificar tablas directamente
   - Si es absolutamente necesario, crea una migración de Laravel
   - Actualiza `database/schema_plaza.sql` inmediatamente

3. **Formato del SQL:**
   - Usar `CREATE TABLE IF NOT EXISTS`
   - Incluir todas las foreign keys
   - Incluir todos los índices
   - Incluir comentarios descriptivos
   - Mantener el orden de creación respetando dependencias

4. **Verificación:**
   - El archivo SQL debe poder recrear toda la base de datos desde cero
   - Probar ejecutando el SQL en una base de datos vacía
   - Verificar que todas las relaciones funcionen correctamente

#### Orden de Creación de Tablas (Respetando Foreign Keys)

1. `plaza_roles` (sin dependencias)
2. `plaza_capabilities` (sin dependencias)
3. `plaza_users` (sin dependencias)
4. `plaza_stores` (depende de `plaza_users`)
5. `plaza_teams` (depende de `plaza_stores`, `plaza_users`)
6. `plaza_memberships` (depende de `plaza_users`, `plaza_stores`, `plaza_roles`)
7. `plaza_role_definitions` (depende de `plaza_roles`, `plaza_capabilities`)
8. `plaza_membership_teams` (depende de `plaza_memberships`, `plaza_teams`)
9. `plaza_custom_overrides` (depende de `plaza_memberships`, `plaza_capabilities`)
10. `plaza_auth_audit` (depende de `plaza_users`)

---

## Tablas del Sistema

### 1. `plaza_roles`
Define los roles disponibles en el sistema.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | SMALLINT UNSIGNED | ID único del rol (autoincremental) |
| `slug` | VARCHAR(50) | Identificador único del rol (ej: "admin", "editor") |
| `name` | VARCHAR(50) | Nombre del rol |
| `description` | VARCHAR(255) | Descripción del rol (nullable) |
| `is_customizable` | BOOLEAN | Si permite personalización de capacidades |

**Índices:**
- PRIMARY KEY: `id`
- UNIQUE: `slug`

---

### 2. `plaza_capabilities`
Define las capacidades/permisos disponibles en el sistema.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | SMALLINT UNSIGNED | ID único de la capacidad |
| `module` | VARCHAR(30) | Módulo al que pertenece (ej: "stores", "orders") |
| `slug` | VARCHAR(50) | Identificador único de la capacidad |
| `label` | VARCHAR(100) | Etiqueta descriptiva |

**Índices:**
- PRIMARY KEY: `id`
- UNIQUE: `slug`

---

### 3. `plaza_users`
Usuarios del sistema Plaza (diferentes de los usuarios admin de Laravel).

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | BIGINT UNSIGNED | ID único del usuario |
| `email` | VARCHAR(150) | Email único del usuario |
| `password_hash` | VARCHAR(255) | Hash de la contraseña |
| `first_name` | VARCHAR(50) | Nombre |
| `last_name` | VARCHAR(50) | Apellido |
| `is_platform_admin` | BOOLEAN | Si es administrador de plataforma |
| `status` | ENUM | Estado: 'pending', 'active', 'suspended' |
| `verification_token` | VARCHAR(100) | Token de verificación (nullable) |
| `reset_token` | VARCHAR(100) | Token de reset de contraseña (nullable) |
| `token_expires_at` | TIMESTAMP | Fecha de expiración del token (nullable) |
| `failed_login_attempts` | SMALLINT UNSIGNED | Intentos fallidos de login |
| `lockout_until` | TIMESTAMP | Bloqueo hasta (nullable) |
| `last_login_at` | TIMESTAMP | Último acceso (nullable) |
| `created_at` | TIMESTAMP | Fecha de creación |
| `deleted_at` | TIMESTAMP | Soft delete (nullable) |

**Índices:**
- PRIMARY KEY: `id`
- UNIQUE: `email`
- INDEX: `deleted_at` (soft deletes)

---

### 4. `plaza_stores`
Tiendas/ecommerce conectadas al sistema Plaza.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | BIGINT UNSIGNED | ID único de la tienda |
| `name` | VARCHAR(100) | Nombre de la tienda |
| `domain_url` | VARCHAR(150) | URL del dominio |
| `platform_type` | ENUM | Tipo: 'woocommerce', 'shopify', 'jumpseller', 'custom' |
| `connection_config` | JSON | Configuración de conexión (nullable) |
| `plaza_api_key` | VARCHAR(64) | Clave API única |
| `owner_id` | BIGINT UNSIGNED | ID del propietario (nullable, FK a `plaza_users`) |
| `logo_url` | VARCHAR(255) | URL del logo (nullable) |
| `created_at` | TIMESTAMP | Fecha de creación |
| `deleted_at` | TIMESTAMP | Soft delete (nullable) |

**Índices:**
- PRIMARY KEY: `id`
- UNIQUE: `plaza_api_key`
- FOREIGN KEY: `owner_id` → `plaza_users.id` (ON DELETE SET NULL)
- INDEX: `deleted_at`

---

### 5. `plaza_teams`
Equipos dentro de las tiendas.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | BIGINT UNSIGNED | ID único del equipo |
| `store_id` | BIGINT UNSIGNED | ID de la tienda (nullable, FK a `plaza_stores`) |
| `name` | VARCHAR(100) | Nombre del equipo |
| `description` | VARCHAR(255) | Descripción (nullable) |
| `created_by` | BIGINT UNSIGNED | ID del creador (nullable, FK a `plaza_users`) |
| `created_at` | TIMESTAMP | Fecha de creación |
| `deleted_at` | TIMESTAMP | Soft delete (nullable) |

**Índices:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `store_id` → `plaza_stores.id` (ON DELETE SET NULL)
- FOREIGN KEY: `created_by` → `plaza_users.id` (ON DELETE SET NULL)
- INDEX: `deleted_at`

---

### 6. `plaza_memberships`
Membresías de usuarios en tiendas (relación usuario-tienda-rol).

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | BIGINT UNSIGNED | ID único de la membresía |
| `user_id` | BIGINT UNSIGNED | ID del usuario (FK a `plaza_users`) |
| `store_id` | BIGINT UNSIGNED | ID de la tienda (FK a `plaza_stores`) |
| `role_id` | SMALLINT UNSIGNED | ID del rol (FK a `plaza_roles`) |
| `is_custom_mode` | BOOLEAN | Si está en modo personalizado |
| `invited_by` | BIGINT UNSIGNED | ID del usuario que invitó (nullable, FK a `plaza_users`) |
| `created_at` | TIMESTAMP | Fecha de creación |
| `deleted_at` | TIMESTAMP | Soft delete (nullable) |

**Índices:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `user_id` → `plaza_users.id` (ON DELETE CASCADE)
- FOREIGN KEY: `store_id` → `plaza_stores.id` (ON DELETE CASCADE)
- FOREIGN KEY: `role_id` → `plaza_roles.id` (ON DELETE RESTRICT)
- FOREIGN KEY: `invited_by` → `plaza_users.id` (ON DELETE SET NULL)
- INDEX: `deleted_at`

---

### 7. `plaza_role_definitions`
Relación muchos a muchos entre roles y capacidades.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `role_id` | SMALLINT UNSIGNED | ID del rol (FK a `plaza_roles`) |
| `capability_id` | SMALLINT UNSIGNED | ID de la capacidad (FK a `plaza_capabilities`) |

**Índices:**
- PRIMARY KEY: (`role_id`, `capability_id`)
- FOREIGN KEY: `role_id` → `plaza_roles.id` (ON DELETE CASCADE)
- FOREIGN KEY: `capability_id` → `plaza_capabilities.id` (ON DELETE CASCADE)

---

### 8. `plaza_membership_teams`
Asignación de membresías a equipos.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | BIGINT UNSIGNED | ID único |
| `membership_id` | BIGINT UNSIGNED | ID de la membresía (FK a `plaza_memberships`) |
| `team_id` | BIGINT UNSIGNED | ID del equipo (FK a `plaza_teams`) |
| `is_team_leader` | BOOLEAN | Si es líder del equipo |
| `assigned_at` | TIMESTAMP | Fecha de asignación |

**Índices:**
- PRIMARY KEY: `id`
- UNIQUE: (`membership_id`, `team_id`)
- FOREIGN KEY: `membership_id` → `plaza_memberships.id` (ON DELETE CASCADE)
- FOREIGN KEY: `team_id` → `plaza_teams.id` (ON DELETE CASCADE)

---

### 9. `plaza_custom_overrides`
Personalizaciones de capacidades para membresías específicas.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `membership_id` | BIGINT UNSIGNED | ID de la membresía (FK a `plaza_memberships`) |
| `capability_id` | SMALLINT UNSIGNED | ID de la capacidad (FK a `plaza_capabilities`) |
| `is_granted` | BOOLEAN | Si la capacidad está concedida o denegada |

**Índices:**
- PRIMARY KEY: (`membership_id`, `capability_id`)
- FOREIGN KEY: `membership_id` → `plaza_memberships.id` (ON DELETE CASCADE)
- FOREIGN KEY: `capability_id` → `plaza_capabilities.id` (ON DELETE CASCADE)

---

### 10. `plaza_auth_audit`
Registro de auditoría de autenticación y eventos de seguridad.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | BIGINT UNSIGNED | ID único del registro |
| `user_id` | BIGINT UNSIGNED | ID del usuario (nullable, FK a `plaza_users`) |
| `event_type` | VARCHAR(50) | Tipo de evento (ej: "login", "logout", "failed_login") |
| `ip_address` | VARCHAR(45) | Dirección IP |
| `user_agent` | VARCHAR(255) | User agent (nullable) |
| `metadata` | JSON | Metadatos adicionales (nullable) |
| `created_at` | TIMESTAMP | Fecha del evento |

**Índices:**
- PRIMARY KEY: `id`
- FOREIGN KEY: `user_id` → `plaza_users.id` (ON DELETE SET NULL)

---

## Modelos Eloquent

Todos los modelos se encuentran en `app/Models/Plaza/`:

- `PlazaRole.php`
- `PlazaCapability.php`
- `PlazaUser.php`
- `PlazaStore.php`
- `PlazaTeam.php`
- `PlazaMembership.php`
- `PlazaMembershipTeam.php`
- `PlazaRoleDefinition.php`
- `PlazaCustomOverride.php`
- `PlazaAuthAudit.php`

### Características de los Modelos

- **Soft Deletes**: Implementado en `PlazaUser`, `PlazaStore`, `PlazaTeam`, `PlazaMembership`
- **Relaciones**: Todas las relaciones `belongsTo` y `hasMany` configuradas
- **Casts**: JSON, booleans, timestamps configurados
- **Nombre de tabla explícito**: Todos los modelos especifican `protected $table = 'plaza_xxx'`

---

## Resources de Filament

### Resources Visibles en el Panel Admin (Para Admins Técnicos)

Los siguientes Resources están disponibles en `/admin` para configuración técnica:

1. **PlazaRoleResource** - Gestión de roles
2. **PlazaCapabilityResource** - Gestión de capacidades
3. **PlazaRoleDefinitionResource** - Asignar capacidades a roles
4. **PlazaAuthAuditResource** - Auditoría (solo lectura)

### Resources Ocultos del Panel Admin

Los siguientes Resources están creados pero **NO** se muestran en Filament, ya que su gestión se realiza desde el frontend Vue:

- `PlazaUserResource`
- `PlazaStoreResource`
- `PlazaTeamResource`
- `PlazaMembershipResource`
- `PlazaMembershipTeamResource`
- `PlazaCustomOverrideResource`

Estos Resources pueden ser útiles para debugging o consultas rápidas, pero la gestión operativa se realiza desde Vue.

---

## APIs y Frontend

### Estado Actual

- ✅ Modelos Eloquent creados
- ✅ Estructura de base de datos lista
- ⚠️ **Pendiente**: Crear controladores API y rutas REST

### Próximos Pasos Recomendados

1. **Crear Controladores API**:
   ```php
   // app/Http/Controllers/Api/PlazaUserController.php
   // app/Http/Controllers/Api/PlazaStoreController.php
   // etc.
   ```

2. **Definir Rutas API**:
   ```php
   // routes/api.php
   Route::apiResource('plaza/users', PlazaUserController::class);
   Route::apiResource('plaza/stores', PlazaStoreController::class);
   ```

3. **Autenticación API**:
   - Implementar Laravel Sanctum o Passport
   - Autenticación contra `plaza_users` (no `users` de Laravel)

---

## Comandos Útiles

### Migraciones

```bash
# Ejecutar migraciones
php artisan migrate

# Ver estado de migraciones
php artisan migrate:status

# Rollback última migración
php artisan migrate:rollback
```

### Base de Datos

```bash
# Ver información de la base de datos
php artisan db:show

# Ver tablas
php artisan db:table plaza_users
```

### Filament

```bash
# Limpiar caché de Filament
php artisan filament:cache-components

# Publicar assets
php artisan filament:assets
```

---

## Notas Importantes

1. **Separación de Usuarios**:
   - `users` (Laravel): Solo para admins técnicos que acceden a Filament
   - `plaza_users`: Usuarios del sistema Plaza gestionados desde Vue

2. **Soft Deletes**:
   - Las tablas con soft deletes no eliminan físicamente los registros
   - Usar `withTrashed()` para incluir registros eliminados en consultas
   - Usar `onlyTrashed()` para ver solo eliminados

3. **Foreign Keys**:
   - `ON DELETE CASCADE`: Elimina registros relacionados (ej: membresías al eliminar usuario)
   - `ON DELETE RESTRICT`: Previene eliminación si hay registros relacionados (ej: roles)
   - `ON DELETE SET NULL`: Establece NULL en relación (ej: tienda al eliminar propietario)

4. **JSON Fields**:
   - `connection_config` en `plaza_stores`: Configuración de conexión con plataformas
   - `metadata` en `plaza_auth_audit`: Metadatos adicionales de eventos

---

## Mantenimiento

### Actualización del Esquema SQL

**Cada vez que se modifique la estructura de la base de datos:**

1. Actualizar las migraciones de Laravel
2. Ejecutar `php artisan migrate`
3. Exportar el esquema actualizado
4. Actualizar `database/schema_plaza.sql`
5. Verificar que el SQL funcione correctamente
6. Commitear ambos archivos (migración + SQL)

---

## Contacto y Soporte

Para dudas sobre la estructura de la base de datos o el sistema, consultar esta documentación o el código fuente en:
- Migraciones: `database/migrations/`
- Modelos: `app/Models/Plaza/`
- Resources: `app/Filament/Resources/Plaza/`

---

**Última actualización**: 2024-12-28  
**Versión del sistema**: 1.0.0

