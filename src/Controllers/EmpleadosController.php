<?php

namespace App\Controllers;

use Medoo\Medoo;

class EmpleadosController
{
    public function __construct(private Medoo $db) {}

    public function index($request, $response): mixed
    {
        return renderView($response, __DIR__ . '/../Views/Empleados/index.php', 'Empleados', []);
    }
}
