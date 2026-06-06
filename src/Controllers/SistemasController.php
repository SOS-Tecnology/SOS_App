<?php

namespace App\Controllers;

use Medoo\Medoo;

class SistemasController
{
    public function __construct(private Medoo $db) {}

    public static function sistemas(): array
    {
        return [
            'nomina'         => ['nombre' => 'Nómina',             'color' => 'teal'],
            'comercial'      => ['nombre' => 'Comercial',          'color' => 'blue'],
            'contable'       => ['nombre' => 'Contable',           'color' => 'indigo'],
            'administrativo' => ['nombre' => 'Administrativo',     'color' => 'emerald'],
            'archivos'       => ['nombre' => 'Archivos Generales', 'color' => 'amber'],
        ];
    }

    public static function opciones(string $sistema): array
    {
        return match ($sistema) {
            'nomina' => [
                ['slug' => 'empleados',          'nombre' => 'Empleados',          'descripcion' => 'Gestión de empleados.'],
                ['slug' => 'novedades',          'nombre' => 'Novedades',          'descripcion' => 'Registro de novedades.'],
                ['slug' => 'informes',           'nombre' => 'Informes',           'descripcion' => 'Informes de nómina.'],
                ['slug' => 'nomina-electronica', 'nombre' => 'Nómina Electrónica', 'descripcion' => 'Emisión de nómina electrónica.'],
            ],
            'comercial' => [
                ['slug' => 'clientes',     'nombre' => 'Clientes',     'descripcion' => 'Gestión de clientes.'],
                ['slug' => 'cotizaciones', 'nombre' => 'Cotizaciones', 'descripcion' => 'Cotizaciones a clientes.'],
                ['slug' => 'pedidos',      'nombre' => 'Pedidos de Venta', 'descripcion' => 'Toma y control de pedidos de venta.', 'url' => '/pedido-venta', 'color' => 'blue'],
                ['slug' => 'facturacion',  'nombre' => 'Facturación',  'descripcion' => 'Facturación a clientes.'],
                ['slug' => 'reportes',     'nombre' => 'Reportes',     'descripcion' => 'Reportes comerciales.'],
            ],
            'contable' => [
                ['slug' => 'comprobantes',      'nombre' => 'Comprobantes',      'descripcion' => 'Comprobantes contables.'],
                ['slug' => 'terceros',          'nombre' => 'Terceros',          'descripcion' => 'Gestión de terceros.'],
                ['slug' => 'informes-contables','nombre' => 'Informes Contables','descripcion' => 'Informes contables.'],
            ],
            'administrativo' => [
                ['slug' => 'proveedores',       'nombre' => 'Proveedores',       'descripcion' => 'Gestión de proveedores.'],
                ['slug' => 'recaudos',          'nombre' => 'Recaudos',          'descripcion' => 'Recaudos.'],
                ['slug' => 'pagos-terceros',    'nombre' => 'Pagos a Terceros',  'descripcion' => 'Pagos a terceros.'],
                ['slug' => 'compras',           'nombre' => 'Compras',           'descripcion' => 'Gestión de compras.'],
                ['slug' => 'gastos',            'nombre' => 'Gastos',            'descripcion' => 'Gestión de gastos.'],
                ['slug' => 'bancos',            'nombre' => 'Bancos',            'descripcion' => 'Movimientos bancarios.'],
                ['slug' => 'gastos-menores',    'nombre' => 'Gastos Menores',    'descripcion' => 'Gastos menores.'],
            ],
            'archivos' => [
                ['slug' => 'impuestos',  'nombre' => 'Impuestos',  'descripcion' => 'Tabla de impuestos.'],
                ['slug' => 'ciudades',   'nombre' => 'Ciudades',   'descripcion' => 'Catálogo de ciudades.'],
                ['slug' => 'vendedores', 'nombre' => 'Vendedores', 'descripcion' => 'Catálogo de vendedores.'],
            ],
            default => [],
        };
    }

    public function principal($request, $response): mixed
    {
        return renderView($response, __DIR__ . '/../Views/Sistemas/principal.php', 'Sistemas', [
            'sistemas' => self::sistemas(),
        ]);
    }

    public function dashboard($request, $response, $args): mixed
    {
        $slug     = $args['slug'] ?? '';
        $sistemas = self::sistemas();

        if (!isset($sistemas[$slug])) {
            return $response->withHeader('Location', '/sistemas')->withStatus(302);
        }

        return renderView($response, __DIR__ . '/../Views/Sistemas/dashboard.php', $sistemas[$slug]['nombre'], [
            'sistema'  => ['slug' => $slug, 'nombre' => $sistemas[$slug]['nombre'], 'color' => $sistemas[$slug]['color']],
            'opciones' => self::opciones($slug),
        ]);
    }
}
