<?php

namespace App\Controllers;

use Medoo\Medoo;

class PermisosController
{
    public function __construct(private Medoo $db) {}

    public function index($request, $response): mixed
    {
        $perfiles = $this->db->select('perfiles', '*', [
            'activo' => 1,
            'ORDER' => ['nombre' => 'ASC']
        ]);

        return renderView($response, __DIR__ . '/../Views/Permisos/index.php', 'Gestionar Permisos', [
            'perfiles' => $perfiles ?: []
        ]);
    }

    public function edit($request, $response, $args): mixed
    {
        $perfilId = (int)$args['id'];
        $perfil = $this->db->get('perfiles', '*', ['id' => $perfilId]);

        if (!$perfil) {
            return $response->withHeader('Location', '/permisos')->withStatus(302);
        }

        $opciones = $this->db->select('opciones', '*', [
            'activo' => 1,
            'ORDER' => ['nombre' => 'ASC']
        ]);

        $permisos = $this->db->select('permisos_perfil', '*', ['perfil_id' => $perfilId]);
        $permisosMap = [];
        foreach ($permisos as $permiso) {
            $permisosMap[$permiso['opcion_id']] = $permiso;
        }

        return renderView($response, __DIR__ . '/../Views/Permisos/edit.php', "Permisos - {$perfil['nombre']}", [
            'perfil' => $perfil,
            'opciones' => $opciones ?: [],
            'permisos' => $permisosMap
        ]);
    }

    public function update($request, $response, $args): mixed
    {
        $perfilId = (int)$args['id'];
        $data = $request->getParsedBody();

        if (!$this->db->has('perfiles', ['id' => $perfilId])) {
            return $response->withHeader('Location', '/permisos')->withStatus(302);
        }

        $opciones = $this->db->select('opciones', 'id');
        $opcionIds = array_column($opciones, 'id');

        foreach ($opcionIds as $opcionId) {
            $prefix = "opcion_{$opcionId}";
            $permiso = $this->db->get('permisos_perfil', 'id', [
                'perfil_id' => $perfilId,
                'opcion_id' => $opcionId
            ]);

            $datosPermiso = [
                'puede_consultar' => isset($data["{$prefix}_consultar"]) ? 1 : 0,
                'puede_crear' => isset($data["{$prefix}_crear"]) ? 1 : 0,
                'puede_modificar' => isset($data["{$prefix}_modificar"]) ? 1 : 0,
                'puede_cambiar_fecha' => isset($data["{$prefix}_cambiar_fecha"]) ? 1 : 0,
                'permiso_especial' => isset($data["{$prefix}_especial"]) ? 1 : 0,
            ];

            if ($permiso) {
                $this->db->update('permisos_perfil', $datosPermiso, [
                    'perfil_id' => $perfilId,
                    'opcion_id' => $opcionId
                ]);
            } else {
                $datosPermiso['perfil_id'] = $perfilId;
                $datosPermiso['opcion_id'] = $opcionId;
                $this->db->insert('permisos_perfil', $datosPermiso);
            }
        }

        $_SESSION['success'] = 'Permisos actualizados correctamente.';
        return $response->withHeader('Location', '/permisos')->withStatus(302);
    }
}
