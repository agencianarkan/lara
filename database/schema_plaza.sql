-- ============================================================================
-- ESQUEMA COMPLETO DE BASE DE DATOS - SISTEMA PLAZA
-- ============================================================================
-- Este archivo contiene el esquema completo de la base de datos del sistema Plaza
-- Fecha de creación: 2024-12-28
-- Base de datos: MariaDB 10.6.23
-- 
-- ⚠️ IMPORTANTE: CUALQUIER CAMBIO EN LA BASE DE DATOS DEBE ACTUALIZARSE
-- EN ESTE ARCHIVO SQL. Ver DOCUMENTACION_PLAZA.md para más detalles.
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ============================================================================
-- TABLA: plaza_roles
-- Descripción: Define los roles disponibles en el sistema (admin, editor, etc.)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `plaza_roles` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(50) NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `is_customizable` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plaza_roles_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: plaza_capabilities
-- Descripción: Define las capacidades/permisos disponibles en el sistema
-- ============================================================================
CREATE TABLE IF NOT EXISTS `plaza_capabilities` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` VARCHAR(30) NOT NULL,
  `slug` VARCHAR(50) NOT NULL,
  `label` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plaza_capabilities_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: plaza_users
-- Descripción: Usuarios del sistema Plaza (diferentes de los usuarios admin de Laravel)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `plaza_users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(150) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `is_platform_admin` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('pending', 'active', 'suspended') NOT NULL,
  `verification_token` VARCHAR(100) DEFAULT NULL,
  `reset_token` VARCHAR(100) DEFAULT NULL,
  `token_expires_at` TIMESTAMP NULL DEFAULT NULL,
  `failed_login_attempts` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `lockout_until` TIMESTAMP NULL DEFAULT NULL,
  `last_login_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plaza_users_email_unique` (`email`),
  KEY `plaza_users_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: plaza_stores
-- Descripción: Tiendas/ecommerce conectadas al sistema Plaza
-- ============================================================================
CREATE TABLE IF NOT EXISTS `plaza_stores` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `domain_url` VARCHAR(150) NOT NULL,
  `platform_type` ENUM('woocommerce', 'shopify', 'jumpseller', 'custom') NOT NULL DEFAULT 'woocommerce',
  `connection_config` JSON DEFAULT NULL,
  `plaza_api_key` VARCHAR(64) NOT NULL,
  `owner_id` BIGINT UNSIGNED DEFAULT NULL,
  `logo_url` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plaza_stores_plaza_api_key_unique` (`plaza_api_key`),
  KEY `plaza_stores_owner_id_foreign` (`owner_id`),
  KEY `plaza_stores_deleted_at_index` (`deleted_at`),
  CONSTRAINT `plaza_stores_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `plaza_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: plaza_teams
-- Descripción: Equipos dentro de las tiendas
-- ============================================================================
CREATE TABLE IF NOT EXISTS `plaza_teams` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `store_id` BIGINT UNSIGNED DEFAULT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_by` BIGINT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `plaza_teams_store_id_foreign` (`store_id`),
  KEY `plaza_teams_created_by_foreign` (`created_by`),
  KEY `plaza_teams_deleted_at_index` (`deleted_at`),
  CONSTRAINT `plaza_teams_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `plaza_stores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `plaza_teams_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `plaza_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: plaza_memberships
-- Descripción: Membresías de usuarios en tiendas (relación usuario-tienda-rol)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `plaza_memberships` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `store_id` BIGINT UNSIGNED NOT NULL,
  `role_id` SMALLINT UNSIGNED NOT NULL,
  `is_custom_mode` TINYINT(1) NOT NULL DEFAULT 0,
  `invited_by` BIGINT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `plaza_memberships_user_id_foreign` (`user_id`),
  KEY `plaza_memberships_store_id_foreign` (`store_id`),
  KEY `plaza_memberships_role_id_foreign` (`role_id`),
  KEY `plaza_memberships_invited_by_foreign` (`invited_by`),
  KEY `plaza_memberships_deleted_at_index` (`deleted_at`),
  CONSTRAINT `plaza_memberships_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `plaza_users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `plaza_memberships_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `plaza_stores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `plaza_memberships_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `plaza_roles` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `plaza_memberships_invited_by_foreign` FOREIGN KEY (`invited_by`) REFERENCES `plaza_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: plaza_role_definitions
-- Descripción: Relación muchos a muchos entre roles y capacidades
-- ============================================================================
CREATE TABLE IF NOT EXISTS `plaza_role_definitions` (
  `role_id` SMALLINT UNSIGNED NOT NULL,
  `capability_id` SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `capability_id`),
  KEY `plaza_role_definitions_capability_id_foreign` (`capability_id`),
  CONSTRAINT `plaza_role_definitions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `plaza_roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `plaza_role_definitions_capability_id_foreign` FOREIGN KEY (`capability_id`) REFERENCES `plaza_capabilities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: plaza_membership_teams
-- Descripción: Asignación de membresías a equipos
-- ============================================================================
CREATE TABLE IF NOT EXISTS `plaza_membership_teams` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `membership_id` BIGINT UNSIGNED NOT NULL,
  `team_id` BIGINT UNSIGNED NOT NULL,
  `is_team_leader` TINYINT(1) NOT NULL DEFAULT 0,
  `assigned_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plaza_membership_teams_membership_id_team_id_unique` (`membership_id`, `team_id`),
  KEY `plaza_membership_teams_team_id_foreign` (`team_id`),
  CONSTRAINT `plaza_membership_teams_membership_id_foreign` FOREIGN KEY (`membership_id`) REFERENCES `plaza_memberships` (`id`) ON DELETE CASCADE,
  CONSTRAINT `plaza_membership_teams_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `plaza_teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: plaza_custom_overrides
-- Descripción: Personalizaciones de capacidades para membresías específicas
-- ============================================================================
CREATE TABLE IF NOT EXISTS `plaza_custom_overrides` (
  `membership_id` BIGINT UNSIGNED NOT NULL,
  `capability_id` SMALLINT UNSIGNED NOT NULL,
  `is_granted` TINYINT(1) NOT NULL,
  PRIMARY KEY (`membership_id`, `capability_id`),
  KEY `plaza_custom_overrides_capability_id_foreign` (`capability_id`),
  CONSTRAINT `plaza_custom_overrides_membership_id_foreign` FOREIGN KEY (`membership_id`) REFERENCES `plaza_memberships` (`id`) ON DELETE CASCADE,
  CONSTRAINT `plaza_custom_overrides_capability_id_foreign` FOREIGN KEY (`capability_id`) REFERENCES `plaza_capabilities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLA: plaza_auth_audit
-- Descripción: Registro de auditoría de autenticación y eventos de seguridad
-- ============================================================================
CREATE TABLE IF NOT EXISTS `plaza_auth_audit` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `event_type` VARCHAR(50) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `metadata` JSON DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `plaza_auth_audit_user_id_foreign` (`user_id`),
  CONSTRAINT `plaza_auth_audit_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `plaza_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- FIN DEL ESQUEMA
-- ============================================================================

