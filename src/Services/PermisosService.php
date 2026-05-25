<?php

namespace App\Services;

use Medoo\Medoo;

class PermisosService
{
    private Medoo $db;
    private ?int $userId = null;
    private ?int $perfilId = null;
    private array $permisos = [];

    public function __construct(Medoo $db, ?int $userId = null)
    {
        $this->db = $db;
        $this->userId = $userId ?? $_SESSION['user']['id'] ?? null;
        $this->cargarPermisos();
    }

    private function cargarPermisos(): void
    {
        if (!$this->userId) {
            return;
        }

        $usuario = $this->db->get('users', ['perfil_id'], ['id' => $this->userId]);
        if (!$usuario || !$usuario['perfil_id']) {
            return;
        }

        $this->perfilId = $usuario['perfil_id'];
        $permisos = $this->db->select('permisos_perfil', '*', ['perfil_id' => $this->perfilId]);

        foreach ($permisos as $permiso) {
            $opcionId = $permiso['opcion_id'];
            $this->permisos[$opcionId] = [
                'consultar' => (bool)$permiso['puede_consultar'],
                'crear' => (bool)$permiso['puede_crear'],
                'modificar' => (bool)$permiso['puede_modificar'],
                'cambiar_fecha' => (bool)$permiso['puede_cambiar_fecha'],
                'especial' => (bool)$permiso['permiso_especial'],
            ];
        }
    }

    public function tieneAcceso(string $opcionSlug): bool
    {
        $opcionId = $this->db->get('opciones', 'id', ['slug' => $opcionSlug]);
        if (!$opcionId) {
            return false;
        }

        return isset($this->permisos[$opcionId]['consultar'])
            && $this->permisos[$opcionId]['consultar'];
    }

    public function puedeConsultar(string $opcionSlug): bool
    {
        return $this->obtenerPermiso($opcionSlug, 'consultar');
    }

    public function puedeCrear(string $opcionSlug): bool
    {
        return $this->obtenerPermiso($opcionSlug, 'crear');
    }

    public function puedeModificar(string $opcionSlug): bool
    {
        return $this->obtenerPermiso($opcionSlug, 'modificar');
    }

    public function puedeCambiarFecha(string $opcionSlug): bool
    {
        return $this->obtenerPermiso($opcionSlug, 'cambiar_fecha');
    }

    public function tienePermisoEspecial(string $opcionSlug): bool
    {
        return $this->obtenerPermiso($opcionSlug, 'especial');
    }

    private function obtenerPermiso(string $opcionSlug, string $tipo): bool
    {
        $opcionId = $this->db->get('opciones', 'id', ['slug' => $opcionSlug]);
        if (!$opcionId) {
            return false;
        }

        return $this->permisos[$opcionId][$tipo] ?? false;
    }

    public function obtenerOpcionesAccesibles(): array
    {
        if (!$this->perfilId) {
            return [];
        }

        return $this->db->select('opciones',
            [
                'opciones.id',
                'opciones.slug',
                'opciones.nombre',
                'opciones.descripcion',
                'opciones.ruta',
                'permisos_perfil.puede_consultar',
                'permisos_perfil.puede_crear',
                'permisos_perfil.puede_modificar',
                'permisos_perfil.puede_cambiar_fecha',
                'permisos_perfil.permiso_especial'
            ],
            [
                'LEFT JOIN' => ['permisos_perfil' => ['opciones.id', '=', 'permisos_perfil.opcion_id']],
                'AND' => [
                    'permisos_perfil.perfil_id' => $this->perfilId,
                    'permisos_perfil.puede_consultar' => 1,
                    'opciones.activo' => 1
                ]
            ]
        );
    }

    public function requierePermiso(string $opcionSlug, string $tipoPermiso = 'consultar'): void
    {
        $tiposValidos = ['consultar', 'crear', 'modificar', 'cambiar_fecha', 'especial'];

        if (!in_array($tipoPermiso, $tiposValidos)) {
            throw new \InvalidArgumentException("Tipo de permiso inválido: {$tipoPermiso}");
        }

        $metodo = match($tipoPermiso) {
            'consultar' => 'puedeConsultar',
            'crear' => 'puedeCrear',
            'modificar' => 'puedeModificar',
            'cambiar_fecha' => 'puedeCambiarFecha',
            'especial' => 'tienePermisoEspecial',
        };

        if (!$this->$metodo($opcionSlug)) {
            header('HTTP/1.1 403 Forbidden');
            exit('Acceso denegado');
        }
    }
}
