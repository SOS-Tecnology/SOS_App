-- ══════════════════════════════════════════════════════════════
-- SOS-Nómina — Script de configuración de Perfiles y Usuarios
-- ══════════════════════════════════════════════════════════════

-- ── Recrear tabla de opciones (limpiando estructura vieja) ──────────
DROP TABLE IF EXISTS opciones;
CREATE TABLE opciones (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug        VARCHAR(100) NOT NULL,
    nombre      VARCHAR(150) NOT NULL,
    descripcion TEXT,
    ruta        VARCHAR(255),
    activo      TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_slug (slug),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Perfiles de acceso ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS perfiles (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    descripcion TEXT,
    activo      TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Permisos por Perfil ────────────────────────────────────────────
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
    INDEX idx_perfil (perfil_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Opciones iniciales de Usuarios y Perfiles ─────────────────────
INSERT INTO opciones (slug, nombre, descripcion, ruta, activo)
VALUES
    ('usuarios', 'Usuarios', 'Gestión de usuarios del sistema', '/usuarios', 1),
    ('perfiles', 'Perfiles', 'Gestión de perfiles y permisos', '/perfiles', 1),
    ('permisos', 'Permisos', 'Configuración de permisos por perfil', '/permisos', 1);

-- ── Perfil administrador y consulta por defecto ───────────────────
INSERT IGNORE INTO perfiles (nombre, descripcion, activo)
VALUES 
    ('Administrador', 'Acceso total al sistema', 1),
    ('Consulta', 'Acceso de solo lectura', 1);

-- ── Permisos: Administrador tiene acceso total a todo ──────────────
INSERT INTO permisos_perfil (perfil_id, opcion_id, puede_consultar, puede_crear, puede_modificar, puede_cambiar_fecha, permiso_especial)
SELECT p.id, o.id, 1, 1, 1, 1, 1
FROM perfiles p, opciones o
WHERE p.nombre = 'Administrador'
ON DUPLICATE KEY UPDATE 
    puede_consultar = 1, puede_crear = 1, puede_modificar = 1, puede_cambiar_fecha = 1, permiso_especial = 1;
