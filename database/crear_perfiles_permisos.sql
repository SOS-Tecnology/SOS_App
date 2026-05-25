-- ══════════════════════════════════════════════════════════════
-- SOS-Nómina: Crear tablas de Perfiles y Permisos
-- BD: SOSCUARON (existente)
-- ══════════════════════════════════════════════════════════════

USE soscuaron;

-- ── Tabla: Perfiles ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS perfiles (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    activo      TINYINT(1) NOT NULL DEFAULT 1,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Agregar columna perfil_id a tabla users si no existe
ALTER TABLE users ADD COLUMN IF NOT EXISTS perfil_id INT UNSIGNED NULL;
ALTER TABLE users ADD FOREIGN KEY IF NOT EXISTS fk_users_perfil (perfil_id)
    REFERENCES perfiles(id) ON DELETE SET NULL;

-- ── Tabla: Opciones (módulos/funcionalidades) ──────────────────
CREATE TABLE IF NOT EXISTS opciones (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug        VARCHAR(100) NOT NULL UNIQUE,
    nombre      VARCHAR(150) NOT NULL,
    descripcion TEXT,
    ruta        VARCHAR(255),
    sistema     VARCHAR(50) NOT NULL DEFAULT 'sistema',
    activo      TINYINT(1) NOT NULL DEFAULT 1,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sistema (sistema),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Tabla: Permisos por Perfil ────────────────────────────────
CREATE TABLE IF NOT EXISTS permisos_perfil (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    perfil_id       INT UNSIGNED NOT NULL,
    opcion_id       INT UNSIGNED NOT NULL,
    puede_consultar TINYINT(1) NOT NULL DEFAULT 0,
    puede_crear     TINYINT(1) NOT NULL DEFAULT 0,
    puede_modificar TINYINT(1) NOT NULL DEFAULT 0,
    puede_cambiar_fecha TINYINT(1) NOT NULL DEFAULT 0,
    permiso_especial TINYINT(1) NOT NULL DEFAULT 0,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_perfil_opcion (perfil_id, opcion_id),
    FOREIGN KEY (perfil_id) REFERENCES perfiles(id) ON DELETE CASCADE,
    FOREIGN KEY (opcion_id) REFERENCES opciones(id) ON DELETE CASCADE,
    INDEX idx_perfil (perfil_id),
    INDEX idx_opcion (opcion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ══════════════════════════════════════════════════════════════
-- Fin: Tablas de Perfiles y Permisos creadas
-- ══════════════════════════════════════════════════════════════
