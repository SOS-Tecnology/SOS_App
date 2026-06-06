
# Integración Orden Pedido completada

## Funcionalidades integradas

- Listado
- Crear
- Editar
- Ver detalle
- Generar PDF
- Consulta dinámica de sucursales

## Archivos agregados

- src/Controllers/OrdenPedidoController.php
- src/Views/ordenpedido/*

## Ajustes realizados

- Registro de rutas Slim
- Registro del controller
- Corrección de rutas Linux
- Corrección de JSON duplicado
- Corrección de valortotal
- Dependencia DomPDF agregada

## IMPORTANTE

Ejecutar:

```bash
composer update
```

o

```bash
composer install
```

## URLs

- /orden-pedido
- /orden-pedido/create

