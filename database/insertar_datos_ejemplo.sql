-- ══════════════════════════════════════════════════════════════
-- Insertar datos de ejemplo: Perfiles, Opciones y Permisos
-- ══════════════════════════════════════════════════════════════

USE soscuaron;

-- ── Insertar Perfiles ─────────────────────────────────────────
INSERT IGNORE INTO perfiles (nombre, descripcion, activo) VALUES
('Administrador', 'Acceso total al sistema', 1),
('Recursos Humanos', 'Gestión de empleados y nómina', 1),
('Contabilidad', 'Acceso a contabilidad e informes', 1),
('Consulta', 'Solo lectura de información', 1);

-- ── Insertar Opciones (Nómina) ────────────────────────────────
INSERT IGNORE INTO opciones (slug, nombre, descripcion, ruta, sistema, activo) VALUES
('empleados', 'Empleados', 'Gestión de empleados', '/empleados', 'nomina', 1),
('novedades', 'Novedades', 'Registro de novedades', '/novedades', 'nomina', 1),
('informes', 'Informes', 'Reportes de nómina', '/informes', 'nomina', 1),
('nomina-electronica', 'Nómina Electrónica', 'Nómina electrónica DIAN', '/nomina-electronica', 'nomina', 1);

-- ── Insertar Opciones (Comercial) ─────────────────────────────
INSERT IGNORE INTO opciones (slug, nombre, descripcion, ruta, sistema, activo) VALUES
('clientes', 'Clientes', 'Gestión de clientes', '/clientes', 'comercial', 1),
('cotizaciones', 'Cotizaciones', 'Cotizaciones y presupuestos', '/cotizaciones', 'comercial', 1),
('pedidos', 'Pedidos', 'Pedidos de clientes', '/pedidos', 'comercial', 1),
('facturacion', 'Facturación', 'Facturas y pagos', '/facturacion', 'comercial', 1),
('reportes-comercial', 'Reportes', 'Reportes comerciales', '/reportes-comercial', 'comercial', 1);

-- ── Insertar Opciones (Contable) ──────────────────────────────
INSERT IGNORE INTO opciones (slug, nombre, descripcion, ruta, sistema, activo) VALUES
('comprobantes', 'Comprobantes', 'Comprobantes contables', '/comprobantes', 'contable', 1),
('terceros', 'Terceros', 'Registro de terceros', '/terceros', 'contable', 1),
('informes-contables', 'Informes', 'Informes contables', '/informes-contables', 'contable', 1);

-- ── Insertar Opciones (Administrativo) ────────────────────────
INSERT IGNORE INTO opciones (slug, nombre, descripcion, ruta, sistema, activo) VALUES
('proveedores', 'Proveedores', 'Gestión de proveedores', '/proveedores', 'administrativo', 1),
('recaudos', 'Recaudos', 'Gestión de recaudos', '/recaudos', 'administrativo', 1),
('pagos-terceros', 'Pagos', 'Pagos a terceros', '/pagos-terceros', 'administrativo', 1),
('compras', 'Compras', 'Gestión de compras', '/compras', 'administrativo', 1),
('gastos', 'Gastos', 'Registro de gastos', '/gastos', 'administrativo', 1),
('bancos', 'Bancos', 'Gestión de bancos', '/bancos', 'administrativo', 1),
('gastos-menores', 'Gastos Menores', 'Caja menor', '/gastos-menores', 'administrativo', 1);

-- ── Insertar Opciones (Archivos) ──────────────────────────────
INSERT IGNORE INTO opciones (slug, nombre, descripcion, ruta, sistema, activo) VALUES
('impuestos', 'Impuestos', 'Configuración de impuestos', '/impuestos', 'archivos', 1),
('ciudades', 'Ciudades', 'Catálogo de ciudades', '/ciudades', 'archivos', 1),
('vendedores', 'Vendedores', 'Gestión de vendedores', '/vendedores', 'archivos', 1);

-- ── Asignar permisos: Administrador (acceso total) ────────────
INSERT IGNORE INTO permisos_perfil (perfil_id, opcion_id, puede_consultar, puede_crear, puede_modificar, puede_cambiar_fecha, permiso_especial)
SELECT p.id, o.id, 1, 1, 1, 1, 1
FROM perfiles p, opciones o
WHERE p.nombre = 'Administrador' AND o.activo = 1;

-- ── Asignar permisos: RH (nómina completo) ───────────────────
INSERT IGNORE INTO permisos_perfil (perfil_id, opcion_id, puede_consultar, puede_crear, puede_modificar, puede_cambiar_fecha, permiso_especial)
SELECT p.id, o.id, 1, 1, 1, 0, 0
FROM perfiles p, opciones o
WHERE p.nombre = 'Recursos Humanos' AND o.slug IN ('empleados', 'novedades', 'informes', 'nomina-electronica');

-- ── Asignar permisos: Contabilidad ───────────────────────────
INSERT IGNORE INTO permisos_perfil (perfil_id, opcion_id, puede_consultar, puede_crear, puede_modificar, puede_cambiar_fecha, permiso_especial)
SELECT p.id, o.id, 1, 1, 1, 0, 0
FROM perfiles p, opciones o
WHERE p.nombre = 'Contabilidad' AND o.slug IN ('comprobantes', 'terceros', 'informes-contables', 'informes');

-- ── Asignar permisos: Consulta (solo lectura) ────────────────
INSERT IGNORE INTO permisos_perfil (perfil_id, opcion_id, puede_consultar, puede_crear, puede_modificar, puede_cambiar_fecha, permiso_especial)
SELECT p.id, o.id, 1, 0, 0, 0, 0
FROM perfiles p, opciones o
WHERE p.nombre = 'Consulta' AND o.activo = 1;

-- ══════════════════════════════════════════════════════════════
-- Fin: Datos de ejemplo insertados
-- ══════════════════════════════════════════════════════════════
