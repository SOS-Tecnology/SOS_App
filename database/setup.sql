-- ══════════════════════════════════════════════════════════════
-- SOS-Nómina — Script de inicialización de base de datos
-- Ejecutar una vez al desplegar el proyecto
-- ══════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS sos_nomina
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sos_nomina;

-- ── Perfiles de acceso ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS perfiles (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    descripcion TEXT,
    activo      TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Perfil administrador por defecto
INSERT IGNORE INTO perfiles (nombre, descripcion, activo)
VALUES ('Administrador', 'Acceso total al sistema', 1),
       ('Recursos Humanos', 'Gestión de empleados y novedades', 1),
       ('Contabilidad', 'Acceso a informes y nómina electrónica', 1),
       ('Consulta', 'Solo lectura', 1);

-- ── Usuarios ──────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(150) NOT NULL,
    email      VARCHAR(255) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    rol        VARCHAR(50)  NOT NULL DEFAULT 'Consulta',
    perfil_id  INT UNSIGNED NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_email (email),
    FOREIGN KEY (perfil_id) REFERENCES perfiles(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuario administrador por defecto (password: Admin2024!)
-- Cambia la contraseña inmediatamente después del primer acceso
INSERT IGNORE INTO users (name, email, password, rol, perfil_id)
VALUES (
    'Administrador',
    'admin@sos-nomina.local',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Administrador',
    (SELECT id FROM perfiles WHERE nombre = 'Administrador' LIMIT 1)
);

-- ── Recuperación de contraseñas ───────────────────────────────────
CREATE TABLE IF NOT EXISTS password_resets (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(255) NOT NULL,
    token      VARCHAR(64)  NOT NULL,
    expires_at DATETIME     NOT NULL,
    INDEX idx_token (token),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Novedades de nómina ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS no_novedad (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo        VARCHAR(20) NOT NULL,
    nombre        VARCHAR(150) NOT NULL,
    tipo          VARCHAR(20) NOT NULL DEFAULT 'Devengado',
    pagar_en      VARCHAR(20) NOT NULL DEFAULT 'Quincenal',
    cantidad      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    porcentaje_he DECIMAL(5,2) DEFAULT NULL,
    novedad_fija  TINYINT(1) NOT NULL DEFAULT 0,
    salario       TINYINT(1) NOT NULL DEFAULT 0,
    hora_extra    TINYINT(1) NOT NULL DEFAULT 0,
    renta_exclu   TINYINT(1) NOT NULL DEFAULT 0,
    formula       VARCHAR(255) DEFAULT NULL,
    activo        TINYINT(1) NOT NULL DEFAULT 1,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO no_novedad (codigo, nombre, tipo, pagar_en, cantidad, porcentaje_he, novedad_fija, salario, hora_extra, renta_exclu, formula, activo)
VALUES
    ('01', 'SALARIO BASICO', 'Devengado', 'Quincenal', 0.00, NULL, 0, 1, 0, 0, 'B/30', 1),
    ('02', 'SUBSIDIO DE TRANSPORTE', 'Devengado', 'Quincenal', 0.00, NULL, 1, 0, 0, 0, 'TRANSP', 1);

-- ── Catálogos de Archivos ─────────────────────────────────────────
-- Las tablas de catálogos se crean automáticamente al acceder
-- a cada opción del menú Archivos. También puedes crearlas manualmente:

CREATE TABLE IF NOT EXISTS arch_tipos_documento (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, codigo VARCHAR(20) NOT NULL,
    descripcion VARCHAR(255) NOT NULL, activo TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS arch_periodos_liquidacion (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, codigo VARCHAR(20) NOT NULL,
    descripcion VARCHAR(255) NOT NULL, activo TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS arch_tipos_trabajador_pila (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, codigo VARCHAR(20) NOT NULL,
    descripcion VARCHAR(255) NOT NULL, activo TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS arch_subtipos_trabajador (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, codigo VARCHAR(20) NOT NULL,
    descripcion VARCHAR(255) NOT NULL, activo TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS arch_tipos_contrato (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, codigo VARCHAR(20) NOT NULL,
    descripcion VARCHAR(255) NOT NULL, activo TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS arch_tipos_incapacidad (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, codigo VARCHAR(20) NOT NULL,
    descripcion VARCHAR(255) NOT NULL, activo TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS arch_tabla_riesgos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, codigo VARCHAR(20) NOT NULL,
    descripcion VARCHAR(255) NOT NULL, activo TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS arch_fondos_solidaridad (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, codigo VARCHAR(20) NOT NULL,
    descripcion VARCHAR(255) NOT NULL, activo TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS arch_eps (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, codigo VARCHAR(20) NOT NULL,
    descripcion VARCHAR(255) NOT NULL, activo TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS arch_fondos_cesantias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, codigo VARCHAR(20) NOT NULL,
    descripcion VARCHAR(255) NOT NULL, activo TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS arch_entidades_riesgos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, codigo VARCHAR(20) NOT NULL,
    descripcion VARCHAR(255) NOT NULL, activo TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS arch_cajas_compensacion (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, codigo VARCHAR(20) NOT NULL,
    descripcion VARCHAR(255) NOT NULL, activo TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
