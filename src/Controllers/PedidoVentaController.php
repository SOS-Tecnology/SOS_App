<?php

namespace App\Controllers;

class PedidoVentaController
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // ══════════════════════════════════════════════════════════════
    // LISTADO
    // ══════════════════════════════════════════════════════════════
    public function index($request, $response)
    {
        $params     = $request->getQueryParams();
        $filtro     = $params['estado']      ?? 'PENDIENTE';
        $codcli     = $params['codcli']      ?? '';
        $fechaDesde = $params['fecha_desde'] ?? '';
        $fechaHasta = $params['fecha_hasta'] ?? '';
        $limite     = (int)($params['limite'] ?? 200);
        if (!in_array($limite, [200, 500, 1000, 0])) $limite = 200;

        $where = ['cabezamov.tm' => 'PV'];
        if ($filtro === 'PENDIENTE')          $where['cabezamov.estado[!]'] = ['C', 'A'];
        elseif (in_array($filtro, ['C','A'])) $where['cabezamov.estado']    = $filtro;
        if ($codcli)     $where['cabezamov.codcp']     = $codcli;
        if ($fechaDesde) $where['cabezamov.fecha[>=]'] = $fechaDesde;
        if ($fechaHasta) $where['cabezamov.fecha[<=]'] = $fechaHasta;
        $where['ORDER'] = ['cabezamov.fecha' => 'DESC'];
        if ($limite > 0) $where['LIMIT'] = $limite;

        $pedidos = $this->db->select('cabezamov', [
            '[>]geclientes'    => ['codcp'  => 'codcli'],
            '[>]geclientesaux' => ['cabezamov.codcp' => 'codcli', 'cabezamov.codsuc' => 'codsuc'],
            '[>]geciudades'    => ['geclientesaux.codciudadsuc' => 'codigociu'],
        ], [
            'cabezamov.documento', 'cabezamov.prefijo',
            'cabezamov.fecha',     'cabezamov.fechent',
            'cabezamov.valortotal','cabezamov.estado',
            'cabezamov.vendedor',  'cabezamov.codsuc',
            'geclientes.nombrecli(cliente)',
            'geciudades.nombreciu(ciudad)',
        ], $where);

        $clientes = $this->db->select('geclientes', ['codcli', 'nombrecli'], ['ORDER' => ['nombrecli' => 'ASC']]);

        return renderView($response, __DIR__ . '/../../src/Views/pedidoventa/index.php', 'Pedidos de Venta', [
            'pedidos'      => $pedidos,
            'clientes'     => $clientes,
            'filtroActual' => $filtro,
            'codcliActual' => $codcli,
            'fechaDesde'   => $fechaDesde,
            'fechaHasta'   => $fechaHasta,
            'limiteActual' => $limite,
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // HELPERS PRIVADOS DE DATOS (reutilizados en create y edit)
    // ══════════════════════════════════════════════════════════════
    private function getFormData(): array
    {
        return [
            'clientes'      => $this->db->select('geclientes', ['codcli', 'nombrecli'], ['ORDER' => ['nombrecli' => 'ASC']]),
            'productos'     => $this->db->select('inrefinv', ['codr', 'descr', 'siiva'], ['tipoprod' => 'V', 'ORDER' => ['codr' => 'ASC']]),
            'tablasPrecios' => $this->db->select('tablaprecios', ['codigo', 'nombre'], ['ORDER' => ['codigo' => 'ASC']]),
            'colores'       => $this->db->select('tablacolor', ['codigo', 'nombre'], ['ORDER' => ['nombre' => 'ASC']]),
            'tallas'        => $this->db->select('tablatalla', ['codigo', 'nombre']),
            'tmConfig'      => $this->db->get('intimovinv', ['comenxitem', 'manejaotrodoc', 'modificavalor'], ['tm' => 'PV']) ?? ['comenxitem' => 0],
        ];
    }

    // ══════════════════════════════════════════════════════════════
    // CREAR
    // ══════════════════════════════════════════════════════════════
    public function create($request, $response)
    {
        return renderView($response, __DIR__ . '/../../src/Views/pedidoventa/create.php', 'Nuevo Pedido de Venta', $this->getFormData());
    }

    // ══════════════════════════════════════════════════════════════
    // GUARDAR
    // ══════════════════════════════════════════════════════════════
    public function store($request, $response)
    {
        $data     = $request->getParsedBody();
        $ultimo   = $this->db->max('cabezamov', 'documento', ['tm' => 'PV']) ?: 0;
        $nuevoDoc = str_pad((int)$ultimo + 1, 8, '0', STR_PAD_LEFT);

        $this->db->insert('cabezamov', [
            'tm'         => 'PV',
            'documento'  => $nuevoDoc,
            'prefijo'    => 'OP',
            'codcp'      => $data['codcp'],
            'codsuc'     => $data['codsuc']   ?: '01',
            'fecha'      => $data['fecha'],
            'fechent'    => $data['fechent'],
            'comen'      => $data['comen']    ?? '',
            'plazo'      => (int)($data['plazo']      ?? 0),
            'otrodoc'    => $data['otrodoc']  ?? '',
            'descuento'  => (float)($data['descuento']  ?? 0),
            'descuento2' => (float)($data['descuento2'] ?? 0),
            'vriva'      => (float)($data['vriva']      ?? 0),
            'retencion'  => (float)($data['retencion']  ?? 0),
            'reteica'    => (float)($data['reteica']    ?? 0),
            'valortotal' => (float)($data['valortotal'] ?? 0),
            'estado'     => ' ',
            'usuario'    => $_SESSION['user']['name'] ?? 'ADMIN',
            'fechacrea'  => date('Y-m-d'),
            'horacrea'   => date('H:i:s'),
            'bodega'     => '01',
            'vendedor'   => '01',
            'moneda'     => 'PESOS',
        ]);

        $this->insertarItems($nuevoDoc, $data['items'] ?? []);

        return $response->withHeader('Location', '/pedido-venta')->withStatus(302);
    }

    // ══════════════════════════════════════════════════════════════
    // AJAX: SUCURSALES
    // ══════════════════════════════════════════════════════════════
    public function getSucursales($request, $response, $args)
    {
        $sucursales = $this->db->select('geclientesaux', ['codsuc', 'nombresuc'], ['codcli' => $args['codcli']]);
        $response->getBody()->write(json_encode($sucursales));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // ══════════════════════════════════════════════════════════════
    // AJAX: INFO CLIENTE
    // ══════════════════════════════════════════════════════════════
    public function getClienteInfo($request, $response, $args)
    {
        $info = $this->db->get('geclientes', [
            '[>]geciudades' => ['codciudadcli' => 'codigociu'],
        ], [
            'geclientes.codcli',        'geclientes.nombrecli',
            'geclientes.direccioncli',  'geclientes.plazocli',
            'geclientes.cupocli',       'geclientes.vlrcreditocli',
            'geciudades.nombreciu',
        ], ['geclientes.codcli' => $args['codcli']]);

        $response->getBody()->write(json_encode($info ?? new \stdClass()));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // ══════════════════════════════════════════════════════════════
    // AJAX: EXISTENCIA
    // ══════════════════════════════════════════════════════════════
    public function getExistencia($request, $response)
    {
        $params = $request->getQueryParams();
        $codr   = $params['codr']   ?? '';
        $bodega = $params['bodega'] ?? '01';
        $row    = $codr ? $this->db->get('inhrefer', ['existen'], ['codr' => $codr, 'posic' => $bodega]) : null;
        $response->getBody()->write(json_encode(['existen' => $row['existen'] ?? null]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // ══════════════════════════════════════════════════════════════
    // AJAX: PRECIO
    // ══════════════════════════════════════════════════════════════
    public function getPrecio($request, $response)
    {
        $params = $request->getQueryParams();
        $codr   = $params['codr']   ?? '';
        $tabpre = $params['tabpre'] ?? '';
        $precio = null;
        if ($codr && $tabpre) {
            $row    = $this->db->get('precios', ['precio'], ['codr' => $codr, 'tablaprecio' => $tabpre]);
            $precio = $row['precio'] ?? null;
        }
        $response->getBody()->write(json_encode(['precio' => $precio]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // ══════════════════════════════════════════════════════════════
    // VER
    // ══════════════════════════════════════════════════════════════
    public function show($request, $response, $args)
    {
        $id     = $args['id'];
        $pedido = $this->db->get('cabezamov', [
            '[>]geclientes' => ['codcp' => 'codcli'],
        ], [
            'cabezamov.documento',  'cabezamov.prefijo',
            'cabezamov.codcp',      'cabezamov.codsuc',
            'cabezamov.fecha',      'cabezamov.fechent',
            'cabezamov.comen',      'cabezamov.estado',
            'cabezamov.valortotal', 'cabezamov.plazo',
            'cabezamov.otrodoc',    'cabezamov.vendedor',
            'cabezamov.descuento',  'cabezamov.vriva',
            'cabezamov.retencion',  'cabezamov.reteica',
            'geclientes.nombrecli(cliente)',
        ], ['cabezamov.documento' => $id, 'cabezamov.tm' => 'PV']);

        $detalles = $this->db->select('cuerpomov', [
            '[>]inrefinv' => ['codr' => 'codr'],
        ], [
            'cuerpomov.item',        'cuerpomov.codr',
            'inrefinv.descr(producto_nombre)',
            'inrefinv.siiva',
            'cuerpomov.tabpreitem',  'cuerpomov.codcolor',
            'cuerpomov.codtalla',    'cuerpomov.cantidad',
            'cuerpomov.valor',       'cuerpomov.descto',
            'cuerpomov.comencpo',
        ], ['cuerpomov.documento' => $id, 'cuerpomov.tm' => 'PV', 'ORDER' => ['cuerpomov.item' => 'ASC']]);

        return renderView($response, __DIR__ . '/../../src/Views/pedidoventa/show.php', 'Pedido #' . $id, [
            'pedido'   => $pedido,
            'detalles' => $detalles,
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // EDITAR
    // ══════════════════════════════════════════════════════════════
    public function edit($request, $response, $args)
    {
        $id     = $args['id'];
        $pedido = $this->db->get('cabezamov', [
            '[>]geclientes' => ['codcp' => 'codcli'],
        ], [
            'cabezamov.documento',  'cabezamov.codcp',
            'cabezamov.codsuc',     'cabezamov.fecha',
            'cabezamov.fechent',    'cabezamov.comen',
            'cabezamov.plazo',      'cabezamov.otrodoc',
            'cabezamov.descuento',  'cabezamov.vriva',
            'cabezamov.retencion',  'cabezamov.reteica',
            'geclientes.nombrecli(cliente)',
        ], ['cabezamov.documento' => $id, 'cabezamov.tm' => 'PV']);

        $detalles = $this->db->select('cuerpomov', [
            'codr', 'tabpreitem', 'codcolor', 'codtalla',
            'cantidad', 'valor', 'descto', 'comencpo',
        ], ['documento' => $id, 'tm' => 'PV', 'ORDER' => ['item' => 'ASC']]);

        $formData = $this->getFormData();

        return renderView($response, __DIR__ . '/../../src/Views/pedidoventa/edit.php', 'Editar Pedido #' . $id, array_merge($formData, [
            'p'        => $pedido,
            'detalles' => $detalles,
        ]));
    }

    // ══════════════════════════════════════════════════════════════
    // ACTUALIZAR
    // ══════════════════════════════════════════════════════════════
    public function update($request, $response, $args)
    {
        $id   = $args['id'];
        $data = $request->getParsedBody();

        $this->db->update('cabezamov', [
            'fechent'    => $data['fechent'],
            'comen'      => $data['comen']   ?? '',
            'plazo'      => (int)($data['plazo']      ?? 0),
            'otrodoc'    => $data['otrodoc'] ?? '',
            'descuento'  => (float)($data['descuento']  ?? 0),
            'descuento2' => (float)($data['descuento2'] ?? 0),
            'vriva'      => (float)($data['vriva']      ?? 0),
            'retencion'  => (float)($data['retencion']  ?? 0),
            'reteica'    => (float)($data['reteica']    ?? 0),
            'valortotal' => (float)($data['valortotal'] ?? 0),
            'fechamod'   => date('Y-m-d'),
        ], ['documento' => $id, 'tm' => 'PV']);

        $this->db->delete('cuerpomov', ['documento' => $id, 'tm' => 'PV']);
        $this->insertarItems($id, $data['items'] ?? []);

        return $response->withHeader('Location', '/pedido-venta')->withStatus(302);
    }

    // ══════════════════════════════════════════════════════════════
    // PDF
    // ══════════════════════════════════════════════════════════════
    public function generarPdf($request, $response, $args)
    {
        $id     = $args['id'];
        $pedido = $this->db->get('cabezamov', [
            '[>]geclientes' => ['codcp' => 'codcli'],
        ], [
            'cabezamov.documento',  'cabezamov.prefijo',
            'cabezamov.codcp',      'cabezamov.codsuc',
            'cabezamov.fecha',      'cabezamov.fechent',
            'cabezamov.comen',      'cabezamov.estado',
            'cabezamov.valortotal', 'cabezamov.plazo',
            'cabezamov.otrodoc',    'cabezamov.descuento',
            'cabezamov.vriva',      'cabezamov.retencion',
            'cabezamov.reteica',
            'geclientes.nombrecli(cliente)',
        ], ['cabezamov.documento' => $id, 'cabezamov.tm' => 'PV']);

        $detalles = $this->db->select('cuerpomov', [
            '[>]inrefinv' => ['codr' => 'codr'],
        ], [
            'cuerpomov.item',        'cuerpomov.codr',
            'inrefinv.descr(producto_nombre)',
            'inrefinv.siiva',
            'cuerpomov.tabpreitem',  'cuerpomov.codcolor',
            'cuerpomov.codtalla',    'cuerpomov.cantidad',
            'cuerpomov.valor',       'cuerpomov.descto',
            'cuerpomov.comencpo',
        ], ['cuerpomov.documento' => $id, 'cuerpomov.tm' => 'PV', 'ORDER' => ['cuerpomov.item' => 'ASC']]);

        ob_start();
        require __DIR__ . '/../../src/Views/pedidoventa/pdf_template.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
    }

    // ══════════════════════════════════════════════════════════════
    // HELPERS PRIVADOS
    // ══════════════════════════════════════════════════════════════
    private function insertarItems(string $documento, array $items): void
    {
        foreach ($items as $index => $item) {
            if (empty($item['codr'])) continue;
            $this->db->insert('cuerpomov', [
                'tm'         => 'PV',
                'documento'  => $documento,
                'prefijo'    => 'OP',
                'item'       => $index + 1,
                'codr'       => $item['codr'],
                'tabpreitem' => $item['tabpreitem'] ?? '',
                'codcolor'   => $item['codcolor']   ?? '',
                'codtalla'   => $item['codtalla']   ?? '',
                'cantidad'   => (float)($item['cantidad'] ?? 0),
                'valor'      => (float)($item['valor']    ?? 0),
                'descto'     => (float)($item['descto']   ?? 0),
                'comencpo'   => $item['comencpo']   ?? '',
            ]);
        }
    }
}
