<?php

namespace App\Controllers;

use Medoo\Medoo;

class NovedadesController
{
    public function __construct(private Medoo $db) {}

    public function index($request, $response): mixed
    {
        $novedades = $this->db->select('no_novedad', '*', [
            'ORDER' => ['id' => 'ASC']
        ]);

        return renderView($response, __DIR__ . '/../Views/Novedades/index.php', 'Novedades', [
            'novedades' => $novedades ?: []
        ]);
    }

    public function create($request, $response): mixed
    {
        return renderView($response, __DIR__ . '/../Views/Novedades/create.php', 'Nueva Novedad', []);
    }

    public function store($request, $response): mixed
    {
        $data = $request->getParsedBody();
        $codigo = trim($data['codigo'] ?? '');
        $nombre = trim($data['nombre'] ?? '');

        if ($codigo === '' || $nombre === '') {
            $_SESSION['errors'] = ['Código y nombre son obligatorios.'];
            return $response->withHeader('Location', '/novedades/create')->withStatus(302);
        }

        if ($this->db->has('no_novedad', ['codigo' => $codigo])) {
            $_SESSION['errors'] = ['Ya existe una novedad con ese código.'];
            return $response->withHeader('Location', '/novedades/create')->withStatus(302);
        }

        $this->db->insert('no_novedad', [
            'codigo'      => $codigo,
            'nombre'      => $nombre,
            'tipo'        => $data['tipo'] ?? 'Devengado',
            'pagar_en'    => $data['pagar_en'] ?? 'Quincenal',
            'cantidad'    => (float)($data['cantidad'] ?? 0),
            'porcentaje_he'=> $data['porcentaje_he'] !== '' ? (float)$data['porcentaje_he'] : null,
            'novedad_fija'=> isset($data['novedad_fija']) ? 1 : 0,
            'salario'     => isset($data['salario']) ? 1 : 0,
            'hora_extra'  => isset($data['hora_extra']) ? 1 : 0,
            'renta_exclu' => isset($data['renta_exclu']) ? 1 : 0,
            'formula'     => trim($data['formula'] ?? ''),
            'activo'      => isset($data['activo']) ? 1 : 0,
        ]);

        $_SESSION['success'] = "Novedad '{$nombre}' creada correctamente.";
        return $response->withHeader('Location', '/novedades')->withStatus(302);
    }

    public function edit($request, $response, $args): mixed
    {
        $id = (int)$args['id'];
        $novedad = $this->db->get('no_novedad', '*', ['id' => $id]);

        if (!$novedad) {
            return $response->withHeader('Location', '/novedades')->withStatus(302);
        }

        return renderView($response, __DIR__ . '/../Views/Novedades/edit.php', 'Editar Novedad', [
            'novedad' => $novedad
        ]);
    }

    public function update($request, $response, $args): mixed
    {
        $id = (int)$args['id'];
        $data = $request->getParsedBody();
        $codigo = trim($data['codigo'] ?? '');
        $nombre = trim($data['nombre'] ?? '');

        if ($codigo === '' || $nombre === '') {
            $_SESSION['errors'] = ['Código y nombre son obligatorios.'];
            return $response->withHeader('Location', '/novedades/' . $id . '/edit')->withStatus(302);
        }

        if ($this->db->has('no_novedad', ['codigo' => $codigo, 'id[!]' => $id])) {
            $_SESSION['errors'] = ['Ya existe otra novedad con ese código.'];
            return $response->withHeader('Location', '/novedades/' . $id . '/edit')->withStatus(302);
        }

        $this->db->update('no_novedad', [
            'codigo'      => $codigo,
            'nombre'      => $nombre,
            'tipo'        => $data['tipo'] ?? 'Devengado',
            'pagar_en'    => $data['pagar_en'] ?? 'Quincenal',
            'cantidad'    => (float)($data['cantidad'] ?? 0),
            'porcentaje_he'=> $data['porcentaje_he'] !== '' ? (float)$data['porcentaje_he'] : null,
            'novedad_fija'=> isset($data['novedad_fija']) ? 1 : 0,
            'salario'     => isset($data['salario']) ? 1 : 0,
            'hora_extra'  => isset($data['hora_extra']) ? 1 : 0,
            'renta_exclu' => isset($data['renta_exclu']) ? 1 : 0,
            'formula'     => trim($data['formula'] ?? ''),
            'activo'      => isset($data['activo']) ? 1 : 0,
        ], ['id' => $id]);

        $_SESSION['success'] = "Novedad '{$nombre}' actualizada correctamente.";
        return $response->withHeader('Location', '/novedades')->withStatus(302);
    }

    public function show($request, $response, $args): mixed
    {
        $id = (int)$args['id'];
        $novedad = $this->db->get('no_novedad', '*', ['id' => $id]);

        if (!$novedad) {
            return $response->withHeader('Location', '/novedades')->withStatus(302);
        }

        return renderView($response, __DIR__ . '/../Views/Novedades/show.php', 'Detalle de Novedad', [
            'novedad' => $novedad
        ]);
    }
}
