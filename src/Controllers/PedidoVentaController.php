<?php

namespace App\Controllers;

class PedidoVentaController
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function index($request, $response)
    {
        $params     = $request->getQueryParams();
        $filtro     = $params['estado']      ?? 'PENDIENTE';
        $codcli     = $params['codcli']      ?? '';
        $fechaDesde = $params['fecha_desde'] ?? '';
        $fechaHasta = $params['fecha_hasta'] ?? '';
        $limite     = (int)($params['limite'] ?? 200);
        if (!in_array($limite, [200, 500, 1000, 0])) $limite = 200;

        // ── WHERE base ────────────────────────────────────────────
        $where = ['cabezamov.tm' => 'PV'];

        if ($filtro === 'PENDIENTE') {
            $where['cabezamov.estado[!]'] = ['C', 'A'];
        } elseif (in_array($filtro, ['C', 'A'])) {
            $where['cabezamov.estado'] = $filtro;
        }

        if ($codcli !== '') {
            $where['cabezamov.codcp'] = $codcli;
        }

        if ($fechaDesde !== '') {
            $where['cabezamov.fecha[>=]'] = $fechaDesde;
        }

        if ($fechaHasta !== '') {
            $where['cabezamov.fecha[<=]'] = $fechaHasta;
        }

        // ── ORDER + LIMIT ─────────────────────────────────────────
        $where['ORDER'] = ['cabezamov.fecha' => 'DESC'];
        if ($limite > 0) {
            $where['LIMIT'] = $limite;
        }

        // ── Query con JOINs ───────────────────────────────────────
        // geclientesaux se une por codcli+codsuc para obtener la ciudad
        $pedidos = $this->db->select('cabezamov', [
            '[>]geclientes'    => ['codcp'  => 'codcli'],
            '[>]geclientesaux' => ['cabezamov.codcp' => 'codcli', 'cabezamov.codsuc' => 'codsuc'],
            '[>]geciudades'    => ['geclientesaux.codciudadsuc' => 'codigociu'],
        ], [
            'cabezamov.documento',
            'cabezamov.prefijo',
            'cabezamov.fecha',
            'cabezamov.fechent',
            'cabezamov.valortotal',
            'cabezamov.estado',
            'cabezamov.vendedor',
            'cabezamov.codsuc',
            'geclientes.nombrecli(cliente)',
            'geciudades.nombreciu(ciudad)',
        ], $where);

        // ── Clientes para el select de filtro ─────────────────────
        $clientes = $this->db->select('geclientes', ['codcli', 'nombrecli'], [
            'ORDER' => ['nombrecli' => 'ASC']
        ]);

        return renderView($response, __DIR__ . '/../../src/Views/pedidoventa/index.php', 'Pedidos de Venta', [
            'pedidos'     => $pedidos,
            'clientes'    => $clientes,
            'filtroActual'=> $filtro,
            'codcliActual'=> $codcli,
            'fechaDesde'  => $fechaDesde,
            'fechaHasta'  => $fechaHasta,
            'limiteActual'=> $limite,
        ]);
    }

    public function create($request, $response)
    {
        $clientes = $this->db->select("geclientes", ["codcli", "nombrecli"]);
        $productos = $this->db->select("inrefinv", [
            "codr",
            "descr"
        ], [
            "tipoprod" => "V"
        ]);
        $tallas = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '6', '8', '10', '12', '14', '16'];
        $colores = ['Blanco', 'Negro', 'Azul Navy', 'Rojo', 'Gris', 'Kaki', 'Verde Militar'];

        return renderView($response, __DIR__ . '/../../src/Views/pedidoventa/create.php', "Nuevo Pedido de Venta", [
            'clientes' => $clientes,
            'productos' => $productos,
            'tallas' => $tallas,
            'colores' => $colores
        ]);
    }

    public function store($request, $response)
    {
        $data = $request->getParsedBody();

        $ultimo = $this->db->max("cabezamov", "documento", ["tm" => "PV"]) ?: 0;
        $nuevoDoc = str_pad((int)$ultimo + 1, 8, "0", STR_PAD_LEFT);

        $total = $this->calcularTotal($data['items'] ?? []);

        $this->db->insert("cabezamov", [
            "tm"        => "PV",
            "documento" => $nuevoDoc,
            "prefijo"   => "OP",
            "codcp"     => $data['codcp'],
            "codsuc"    => $data['codsuc'] ?: '01',
            "fecha"     => $data['fecha'],
            "fechent"   => $data['fechent'],
            "comen"     => $data['comen'] ?? '',
            "valortotal"=> $total,
            "estado"    => " ",
            "usuario"   => $_SESSION['user_id'] ?? 'ADMIN',
            "fechacrea" => date('Y-m-d'),
            "horacrea"  => date('H:i:s'),
            "bodega"    => "01",
            "vendedor"  => "01",
            "moneda"    => "PESOS"
        ]);

        foreach ($data['items'] ?? [] as $index => $item) {
            if (empty($item['codr'])) continue;
            $this->db->insert("cuerpomov", [
                "tm"       => "PV",
                "documento"=> $nuevoDoc,
                "prefijo"  => "OP",
                "codr"     => $item['codr'],
                "codtalla" => $item['codtalla'],
                "codcolor" => $item['codcolor'],
                "cantidad" => $item['cantidad'],
                "valor"    => $item['valor'],
                "comencpo" => $item['comencpo'],
                "item"     => $index + 1
            ]);
        }

        return $response->withHeader('Location', '/pedido-venta')->withStatus(302);
    }

    public function getSucursales($request, $response, $args)
    {
        $codcli = $args['codcli'];
        $sucursales = $this->db->select("geclientesaux", ["codsuc", "nombresuc"], ["codcli" => $codcli]);
        $response->getBody()->write(json_encode($sucursales));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function calcularTotal($items)
    {
        $total = 0;
        foreach ($items as $item) {
            $total += ($item['cantidad'] * $item['valor']);
        }
        return $total;
    }

    public function show($request, $response, $args)
    {
        $id = $args['id'];

        $pedido = $this->db->get("cabezamov", [
            "[>]geclientes" => ["codcp" => "codcli"]
        ], [
            "cabezamov.documento",
            "cabezamov.codcp",
            "cabezamov.codsuc",
            "cabezamov.fecha",
            "cabezamov.fechent",
            "cabezamov.comen",
            "cabezamov.estado",
            "cabezamov.valortotal",
            "geclientes.nombrecli(cliente)"
        ], [
            "cabezamov.documento" => $id,
            "cabezamov.tm" => "PV"
        ]);

        $detalles = $this->db->select("cuerpomov", [
            "[>]inrefinv" => ["codr" => "codr"]
        ], [
            "cuerpomov.codr",
            "inrefinv.descr(producto_nombre)",
            "cuerpomov.codtalla",
            "cuerpomov.codcolor",
            "cuerpomov.cantidad",
            "cuerpomov.valor",
            "cuerpomov.comencpo"
        ], [
            "cuerpomov.documento" => $id,
            "cuerpomov.tm" => "PV"
        ]);

        return renderView($response, __DIR__ . '/../../src/Views/pedidoventa/show.php', "Consulta Pedido de Venta #" . $id, [
            'pedido'   => $pedido,
            'detalles' => $detalles
        ]);
    }

    public function edit($request, $response, $args)
    {
        $id = $args['id'];

        $pedido = $this->db->get("cabezamov", [
            "[>]geclientes" => ["codcp" => "codcli"]
        ], [
            "cabezamov.documento",
            "cabezamov.codcp",
            "cabezamov.codsuc",
            "cabezamov.fecha",
            "cabezamov.fechent",
            "cabezamov.comen",
            "geclientes.nombrecli(cliente)"
        ], [
            "cabezamov.documento" => $id,
            "cabezamov.tm" => "PV"
        ]);

        $detalles = $this->db->select("cuerpomov", [
            "codr", "codtalla", "codcolor", "cantidad", "valor", "comencpo"
        ], [
            "documento" => $id,
            "tm" => "PV"
        ]);

        $clientes  = $this->db->select("geclientes", ["codcli", "nombrecli"]);
        $productos = $this->db->select("inrefinv", ["codr", "descr"], ["tipoprod" => "V"]);

        $tallas = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '6', '8', '10', '12', '14', '16'];
        $colores = ['Blanco', 'Negro', 'Azul Navy', 'Rojo', 'Gris', 'Kaki', 'Verde Militar'];

        return renderView($response, __DIR__ . '/../../src/Views/pedidoventa/edit.php', "Editar Pedido de Venta #" . $id, [
            'p'        => $pedido,
            'detalles' => $detalles,
            'clientes' => $clientes,
            'productos'=> $productos,
            'tallas'   => $tallas,
            'colores'  => $colores
        ]);
    }

    public function update($request, $response, $args)
    {
        $id   = $args['id'];
        $data = $request->getParsedBody();

        $nuevoTotal = $this->calcularTotal($data['items'] ?? []);

        $this->db->update("cabezamov", [
            "fechent"   => $data['fechent'],
            "comen"     => $data['comen'],
            "valortotal"=> $nuevoTotal,
            "fechamod"  => date('Y-m-d')
        ], [
            "documento" => $id,
            "tm" => "PV"
        ]);

        $this->db->delete("cuerpomov", ["documento" => $id, "tm" => "PV"]);

        foreach ($data['items'] ?? [] as $index => $item) {
            if (empty($item['codr'])) continue;
            $this->db->insert("cuerpomov", [
                "tm"       => "PV",
                "documento"=> $id,
                "prefijo"  => "OP",
                "codr"     => $item['codr'],
                "codtalla" => $item['codtalla'],
                "codcolor" => $item['codcolor'],
                "cantidad" => $item['cantidad'],
                "valor"    => $item['valor'],
                "comencpo" => $item['comencpo'],
                "item"     => $index + 1
            ]);
        }

        return $response->withHeader('Location', '/pedido-venta')->withStatus(302);
    }

    public function generarPdf($request, $response, $args)
    {
        $id = $args['id'];

        $pedido = $this->db->get("cabezamov", [
            "[>]geclientes" => ["codcp" => "codcli"]
        ], [
            "cabezamov.documento",
            "cabezamov.codcp",
            "cabezamov.codsuc",
            "cabezamov.fecha",
            "cabezamov.fechent",
            "cabezamov.comen",
            "cabezamov.estado",
            "cabezamov.valortotal",
            "geclientes.nombrecli(cliente)"
        ], [
            "cabezamov.documento" => $id,
            "cabezamov.tm" => "PV"
        ]);

        $detalles = $this->db->select("cuerpomov", [
            "[>]inrefinv" => ["codr" => "codr"]
        ], [
            "cuerpomov.codr",
            "inrefinv.descr(producto_nombre)",
            "cuerpomov.codtalla",
            "cuerpomov.codcolor",
            "cuerpomov.cantidad",
            "cuerpomov.valor",
            "cuerpomov.comencpo"
        ], [
            "cuerpomov.documento" => $id,
            "cuerpomov.tm" => "PV"
        ]);

        ob_start();
        require __DIR__ . '/../../src/Views/pedidoventa/pdf_template.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
    }
}
