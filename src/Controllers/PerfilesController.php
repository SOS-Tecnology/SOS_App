<?php

namespace App\Controllers;

use Medoo\Medoo;

class PerfilesController
{
    public function __construct(private Medoo $db) {}

    public function index($request, $response): mixed
    {
        $perfiles = $this->db->pdo->query("
            SELECT p.*, COUNT(u.id) AS total_usuarios
            FROM perfiles p
            LEFT JOIN users u ON u.perfil_id = p.id
            GROUP BY p.id
            ORDER BY p.nombre ASC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        return renderView($response, __DIR__ . '/../Views/Perfiles/index.php', 'Perfiles', [
            'perfiles' => $perfiles
        ]);
    }

    public function create($request, $response): mixed
    {
        return renderView($response, __DIR__ . '/../Views/Perfiles/create.php', 'Nuevo Perfil', []);
    }

    public function store($request, $response): mixed
    {
        $data = $request->getParsedBody();
        $nombre = trim($data['nombre'] ?? '');

        if ($nombre === '') {
            $_SESSION['errors'] = ['El nombre del perfil es obligatorio.'];
            return $response->withHeader('Location', '/perfiles/create')->withStatus(302);
        }

        if ($this->db->has('perfiles', ['nombre' => $nombre])) {
            $_SESSION['errors'] = ['Ya existe un perfil con ese nombre.'];
            return $response->withHeader('Location', '/perfiles/create')->withStatus(302);
        }

        $this->db->insert('perfiles', [
            'nombre'      => $nombre,
            'descripcion' => trim($data['descripcion'] ?? ''),
            'activo'      => (int)($data['activo'] ?? 1),
        ]);

        $_SESSION['success'] = "Perfil «{$nombre}» creado correctamente.";
        return $response->withHeader('Location', '/perfiles')->withStatus(302);
    }

    public function edit($request, $response, $args): mixed
    {
        $perfil = $this->db->get('perfiles', '*', ['id' => (int)$args['id']]);
        if (!$perfil) {
            return $response->withHeader('Location', '/perfiles')->withStatus(302);
        }

        return renderView($response, __DIR__ . '/../Views/Perfiles/edit.php', 'Editar Perfil', [
            'perfil' => $perfil
        ]);
    }

    public function update($request, $response, $args): mixed
    {
        $id   = (int)$args['id'];
        $data = $request->getParsedBody();
        $nombre = trim($data['nombre'] ?? '');

        if ($nombre === '') {
            $_SESSION['errors'] = ['El nombre del perfil es obligatorio.'];
            return $response->withHeader('Location', '/perfiles/' . $id . '/edit')->withStatus(302);
        }

        if ($this->db->has('perfiles', ['nombre' => $nombre, 'id[!]' => $id])) {
            $_SESSION['errors'] = ['Ya existe un perfil con ese nombre.'];
            return $response->withHeader('Location', '/perfiles/' . $id . '/edit')->withStatus(302);
        }

        $this->db->update('perfiles', [
            'nombre'      => $nombre,
            'descripcion' => trim($data['descripcion'] ?? ''),
            'activo'      => (int)($data['activo'] ?? 1),
        ], ['id' => $id]);

        $_SESSION['success'] = "Perfil actualizado correctamente.";
        return $response->withHeader('Location', '/perfiles')->withStatus(302);
    }

    public function delete($request, $response, $args): mixed
    {
        $id = (int)$args['id'];
        $count = $this->db->count('users', ['perfil_id' => $id]);

        if ($count > 0) {
            $_SESSION['errors'] = ['No se puede eliminar: hay usuarios asignados a este perfil.'];
            return $response->withHeader('Location', '/perfiles')->withStatus(302);
        }

        $this->db->delete('perfiles', ['id' => $id]);
        $_SESSION['success'] = 'Perfil eliminado correctamente.';
        return $response->withHeader('Location', '/perfiles')->withStatus(302);
    }
}
