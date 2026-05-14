<?php

namespace App\Controllers;

use Medoo\Medoo;

class ArchivosController
{
    private static array $catalogos = [
        'tipos-documento'      => ['tabla' => 'arch_tipos_documento',     'titulo' => 'Tipos de Documento'],
        'periodos-liquidacion' => ['tabla' => 'arch_periodos_liquidacion', 'titulo' => 'Períodos de Liquidación'],
        'tipos-trabajador-pila'=> ['tabla' => 'arch_tipos_trabajador_pila','titulo' => 'Tipos de Trabajador PILA'],
        'subtipos-trabajador'  => ['tabla' => 'arch_subtipos_trabajador',  'titulo' => 'Sub Tipos de Trabajador'],
        'tipos-contrato'       => ['tabla' => 'arch_tipos_contrato',       'titulo' => 'Tipos de Contrato'],
        'tipos-incapacidad'    => ['tabla' => 'arch_tipos_incapacidad',    'titulo' => 'Tipos de Incapacidad'],
        'tabla-riesgos'        => ['tabla' => 'arch_tabla_riesgos',        'titulo' => 'Tabla de Riesgos Profesionales'],
        'fondos-solidaridad'   => ['tabla' => 'arch_fondos_solidaridad',   'titulo' => 'Fondos de Solidaridad Pensional'],
        'eps'                  => ['tabla' => 'arch_eps',                  'titulo' => 'Empresas Prestadoras de Salud (EPS)'],
        'fondos-cesantias'     => ['tabla' => 'arch_fondos_cesantias',     'titulo' => 'Fondos de Cesantías'],
        'entidades-riesgos'    => ['tabla' => 'arch_entidades_riesgos',    'titulo' => 'Entidades Administradoras de Riesgos Profesionales'],
        'cajas-compensacion'   => ['tabla' => 'arch_cajas_compensacion',   'titulo' => 'Cajas de Compensación Familiar'],
    ];

    public function __construct(private Medoo $db) {}

    private function resolveCatalogo(string $slug): ?array
    {
        return self::$catalogos[$slug] ?? null;
    }

    public function index($request, $response, $args): mixed
    {
        $slug = $args['catalogo'] ?? '';
        $cat  = $this->resolveCatalogo($slug);

        if (!$cat) {
            return $response->withHeader('Location', '/dashboard_home')->withStatus(302);
        }

        $this->crearTablasSiNoExisten($cat['tabla']);

        $registros = $this->db->select($cat['tabla'], '*', ['ORDER' => ['codigo' => 'ASC']]);

        return renderView($response, __DIR__ . '/../Views/Archivos/catalogo.php', $cat['titulo'], [
            'titulo'    => $cat['titulo'],
            'catalogo'  => $slug,
            'registros' => $registros ?: [],
        ]);
    }

    public function store($request, $response, $args): mixed
    {
        $slug = $args['catalogo'] ?? '';
        $cat  = $this->resolveCatalogo($slug);
        if (!$cat) {
            return $response->withHeader('Location', '/dashboard_home')->withStatus(302);
        }

        $data   = $request->getParsedBody();
        $codigo = trim($data['codigo'] ?? '');
        $desc   = trim($data['descripcion'] ?? '');

        if ($codigo === '' || $desc === '') {
            $_SESSION['errors'] = ['Código y descripción son obligatorios.'];
            return $response->withHeader('Location', '/archivos/' . $slug)->withStatus(302);
        }

        if ($this->db->has($cat['tabla'], ['codigo' => $codigo])) {
            $_SESSION['errors'] = ["Ya existe un registro con el código «{$codigo}»."];
            return $response->withHeader('Location', '/archivos/' . $slug)->withStatus(302);
        }

        $this->db->insert($cat['tabla'], [
            'codigo'      => $codigo,
            'descripcion' => $desc,
            'activo'      => (int)($data['activo'] ?? 1),
        ]);

        $_SESSION['success'] = 'Registro creado correctamente.';
        return $response->withHeader('Location', '/archivos/' . $slug)->withStatus(302);
    }

    public function update($request, $response, $args): mixed
    {
        $slug = $args['catalogo'] ?? '';
        $id   = (int)($args['id'] ?? 0);
        $cat  = $this->resolveCatalogo($slug);
        if (!$cat) {
            return $response->withHeader('Location', '/dashboard_home')->withStatus(302);
        }

        $data   = $request->getParsedBody();
        $codigo = trim($data['codigo'] ?? '');
        $desc   = trim($data['descripcion'] ?? '');

        if ($codigo === '' || $desc === '') {
            $_SESSION['errors'] = ['Código y descripción son obligatorios.'];
            return $response->withHeader('Location', '/archivos/' . $slug)->withStatus(302);
        }

        if ($this->db->has($cat['tabla'], ['codigo' => $codigo, 'id[!]' => $id])) {
            $_SESSION['errors'] = ["Ya existe otro registro con el código «{$codigo}»."];
            return $response->withHeader('Location', '/archivos/' . $slug)->withStatus(302);
        }

        $this->db->update($cat['tabla'], [
            'codigo'      => $codigo,
            'descripcion' => $desc,
            'activo'      => (int)($data['activo'] ?? 1),
        ], ['id' => $id]);

        $_SESSION['success'] = 'Registro actualizado correctamente.';
        return $response->withHeader('Location', '/archivos/' . $slug)->withStatus(302);
    }

    public function delete($request, $response, $args): mixed
    {
        $slug = $args['catalogo'] ?? '';
        $id   = (int)($args['id'] ?? 0);
        $cat  = $this->resolveCatalogo($slug);
        if (!$cat) {
            return $response->withHeader('Location', '/dashboard_home')->withStatus(302);
        }

        $this->db->delete($cat['tabla'], ['id' => $id]);
        $_SESSION['success'] = 'Registro eliminado.';
        return $response->withHeader('Location', '/archivos/' . $slug)->withStatus(302);
    }

    // Crea la tabla del catálogo si aún no existe en la BD
    private function crearTablasSiNoExisten(string $tabla): void
    {
        $this->db->pdo->exec("
            CREATE TABLE IF NOT EXISTS `{$tabla}` (
                id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                codigo      VARCHAR(20)  NOT NULL,
                descripcion VARCHAR(255) NOT NULL,
                activo      TINYINT(1)   NOT NULL DEFAULT 1,
                UNIQUE KEY uq_codigo (codigo)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
}
