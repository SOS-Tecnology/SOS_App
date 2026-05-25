# Crear Tablas de Perfiles y Permisos

## Ejecución del Script SQL

Ejecuta el archivo `database/crear_perfiles_permisos.sql` en tu BD `soscuaron`:

### Opción 1: MySQL Workbench
1. Abre Workbench
2. Selecciona BD `soscuaron`
3. Abre archivo `database/crear_perfiles_permisos.sql`
4. Ejecuta (Ctrl+Shift+Enter)

### Opción 2: phpMyAdmin
1. Ve a phpMyAdmin
2. Selecciona BD `soscuaron`
3. Tab "SQL"
4. Copia contenido de `database/crear_perfiles_permisos.sql`
5. Ejecuta

### Opción 3: Línea de comandos
```bash
mysql -h localhost -u root -p1234 soscuaron < database/crear_perfiles_permisos.sql
```

## Qué hace el script

✅ Crea tabla `perfiles`
✅ Agrega columna `perfil_id` a tabla `users`
✅ Crea tabla `opciones`
✅ Crea tabla `permisos_perfil`

## Verificación

Después de ejecutar:
```sql
SHOW TABLES LIKE 'perfil%';
SHOW TABLES LIKE 'opciones';
SHOW TABLES LIKE 'permisos%';
```

Deberías ver las 3 nuevas tablas creadas.
