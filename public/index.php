<?php
session_name('SOSNOMINA');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'secure'   => false,
    'samesite' => 'Lax'
]);
session_start();

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config.php';

use Slim\Factory\AppFactory;
use Dotenv\Dotenv;
use App\Lib\Database;
use App\Middleware\AuthMiddleware;
use App\Controllers\PerfilesController;
use App\Controllers\PermisosController;
use App\Controllers\SistemasController;
use App\Controllers\PedidoVentaController;
use App\Services\PermisosService;

// ── Entorno ───────────────────────────────────────────────────────
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// ── Base de datos ─────────────────────────────────────────────────
try {
    $GLOBALS['db'] = Database::connect();
} catch (\RuntimeException $e) {
    $_SESSION['db_error'] = true;
    header('Location: /login');
    exit;
}

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

ini_set('display_errors', 1);
error_reporting(E_ALL);

// ── Helper renderView ─────────────────────────────────────────────
function renderView($response, $viewPath, $title, $data = [])
{
    extract($data);
    ob_start();
    include $viewPath;
    $content = ob_get_clean();
    include __DIR__ . '/../src/Views/layouts/dashboard.php';
    return $response;
}

// ── Helper de Permisos ────────────────────────────────────────────
function permisos(): PermisosService
{
    static $permisos = null;
    if ($permisos === null) {
        $permisos = new PermisosService($GLOBALS['db']);
    }
    return $permisos;
}

// ─────────────────────────────────────────────────────────────────
// RUTAS PÚBLICAS
// ─────────────────────────────────────────────────────────────────

$app->get('/', function ($request, $response) {
    $loc = isset($_SESSION['user']) ? '/sistemas' : '/login';
    return $response->withHeader('Location', $loc)->withStatus(302);
});

// ── Login ─────────────────────────────────────────────────────────
$app->get('/login', function ($request, $response) {
    ob_start();
    include __DIR__ . '/../src/Views/Auth/login.php';
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->post('/login', function ($request, $response) {
    $data     = $request->getParsedBody();
    $email    = trim($data['email']    ?? '');
    $password = trim($data['password'] ?? '');

    if ($email === '' || $password === '') {
        $_SESSION['errors'] = ['Todos los campos son obligatorios.'];
        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    $user = $GLOBALS['db']->get('users', '*', ['email' => $email]);

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['errors'] = ['Credenciales incorrectas.'];
        return $response->withHeader('Location', '/login')->withStatus(302);
    }

    $_SESSION['user'] = [
        'id'       => $user['id'],
        'name'     => $user['name'],
        'email'    => $user['email'],
        'rol'      => $user['rol'],
        'perfil_id' => $user['perfil_id'] ?? null,
    ];
    $_SESSION['LAST_ACTIVITY'] = time();

    return $response->withHeader('Location', '/sistemas')->withStatus(302);
});

$app->get('/logout', function ($request, $response) {
    session_unset();
    session_destroy();
    return $response->withHeader('Location', '/login')->withStatus(302);
});

// ── Recuperación de contraseña ────────────────────────────────────
$app->get('/forgot-password', function ($request, $response) {
    ob_start();
    include __DIR__ . '/../src/Views/Auth/forgot-password.php';
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->post('/forgot-password', function ($request, $response) {
    $email = trim($request->getParsedBody()['email'] ?? '');
    $user  = $GLOBALS['db']->get("users", ["id", "name", "email"], ["email" => $email]);

    $_SESSION['success'] = "Si el correo está registrado, recibirás un enlace en breve.";

    if ($user) {
        $GLOBALS['db']->query("
            CREATE TABLE IF NOT EXISTS password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                token VARCHAR(64) NOT NULL,
                expires_at DATETIME NOT NULL,
                INDEX (token), INDEX (email)
            )
        ");

        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600);

        $GLOBALS['db']->delete("password_resets", ["email" => $email]);
        $GLOBALS['db']->insert("password_resets", [
            "email" => $email,
            "token" => $token,
            "expires_at" => $expires
        ]);

        $url  = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/reset-password/' . $token;
        $body = "Hola {$user['name']},\n\nRestablece tu contraseña (válido 1 hora):\n\n{$url}\n\nSOS-Nómina";
        @mail(
            $email,
            "Restablecer contraseña - SOS Nómina",
            $body,
            "From: noreply@sos-nomina.local\r\nContent-Type: text/plain; charset=UTF-8"
        );
    }

    return $response->withHeader('Location', '/forgot-password')->withStatus(302);
});

$app->get('/reset-password/{token}', function ($request, $response, $args) {
    $token = $args['token'];
    $reset = $GLOBALS['db']->get(
        "password_resets",
        "*",
        ["token" => $token, "expires_at[>]" => date('Y-m-d H:i:s')]
    );

    if (!$reset) {
        $_SESSION['errors'] = ["El enlace no es válido o ha expirado."];
        return $response->withHeader('Location', '/forgot-password')->withStatus(302);
    }

    ob_start();
    include __DIR__ . '/../src/Views/Auth/reset-password.php';
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->post('/reset-password/{token}', function ($request, $response, $args) {
    $token    = $args['token'];
    $password = $request->getParsedBody()['password'] ?? '';
    $reset    = $GLOBALS['db']->get(
        "password_resets",
        "*",
        ["token" => $token, "expires_at[>]" => date('Y-m-d H:i:s')]
    );

    if (!$reset) {
        $_SESSION['errors'] = ["El enlace no es válido o ha expirado."];
        return $response->withHeader('Location', '/forgot-password')->withStatus(302);
    }
    if (strlen($password) < 8) {
        $_SESSION['errors'] = ["La contraseña debe tener al menos 8 caracteres."];
        return $response->withHeader('Location', '/reset-password/' . $token)->withStatus(302);
    }

    $GLOBALS['db']->update(
        "users",
        ["password" => password_hash($password, PASSWORD_DEFAULT)],
        ["email"    => $reset['email']]
    );
    $GLOBALS['db']->delete("password_resets", ["token" => $token]);

    $_SESSION['success'] = "Contraseña actualizada. Ya puedes iniciar sesión.";
    return $response->withHeader('Location', '/login')->withStatus(302);
});

// ─────────────────────────────────────────────────────────────────
// RUTAS PROTEGIDAS
// ─────────────────────────────────────────────────────────────────
$authMiddleware = new AuthMiddleware();

$app->group('', function ($group) {

    // ─────────────────────────────────────────────────────────────
    // PEDIDOS DE VENTA
    // ─────────────────────────────────────────────────────────────

    $group->get('/pedido-venta', function ($request, $response) {
        $ctrl = new PedidoVentaController($GLOBALS['db']);
        return $ctrl->index($request, $response);
    });

    $group->get('/pedido-venta/create', function ($request, $response) {
        $ctrl = new PedidoVentaController($GLOBALS['db']);
        return $ctrl->create($request, $response);
    });

    $group->post('/pedido-venta/store', function ($request, $response) {
        $ctrl = new PedidoVentaController($GLOBALS['db']);
        return $ctrl->store($request, $response);
    });

    $group->get('/pedido-venta/show/{id}', function ($request, $response, $args) {
        $ctrl = new PedidoVentaController($GLOBALS['db']);
        return $ctrl->show($request, $response, $args);
    });

    $group->get('/pedido-venta/edit/{id}', function ($request, $response, $args) {
        $ctrl = new PedidoVentaController($GLOBALS['db']);
        return $ctrl->edit($request, $response, $args);
    });

    $group->post('/pedido-venta/update/{id}', function ($request, $response, $args) {
        $ctrl = new PedidoVentaController($GLOBALS['db']);
        return $ctrl->update($request, $response, $args);
    });

    $group->get('/pedido-venta/sucursales/{codcli}', function ($request, $response, $args) {
        $ctrl = new PedidoVentaController($GLOBALS['db']);
        return $ctrl->getSucursales($request, $response, $args);
    });

    $group->get('/pedido-venta/pdf/{id}', function ($request, $response, $args) {
        $ctrl = new PedidoVentaController($GLOBALS['db']);
        return $ctrl->generarPdf($request, $response, $args);
    });

    $group->get('/pedido-venta/cliente-info/{codcli}', function ($req, $res, $args) {
        return (new PedidoVentaController($GLOBALS['db']))->getClienteInfo($req, $res, $args);
    });

    $group->get('/pedido-venta/precio', function ($req, $res) {
        return (new PedidoVentaController($GLOBALS['db']))->getPrecio($req, $res);
    });
    $group->get('/pedido-venta/existencia', function ($req, $res) {
        return (new PedidoVentaController($GLOBALS['db']))->getExistencia($req, $res);
    });

    // ── Sistemas (inicio) ─────────────────────────────────────────
    $group->get('/sistemas', function ($request, $response) {
        $ctrl = new SistemasController($GLOBALS['db']);
        return $ctrl->principal($request, $response);
    });

    $group->get('/sistemas/{slug}', function ($request, $response, $args) {
        $ctrl = new SistemasController($GLOBALS['db']);
        return $ctrl->dashboard($request, $response, $args);
    });

    // ── Perfiles ──────────────────────────────────────────────────
    $group->get('/perfiles', function ($request, $response) {
        $ctrl = new PerfilesController($GLOBALS['db']);
        return $ctrl->index($request, $response);
    });

    $group->get('/perfiles/create', function ($request, $response) {
        $ctrl = new PerfilesController($GLOBALS['db']);
        return $ctrl->create($request, $response);
    });

    $group->post('/perfiles/store', function ($request, $response) {
        $ctrl = new PerfilesController($GLOBALS['db']);
        return $ctrl->store($request, $response);
    });

    $group->get('/perfiles/{id}/edit', function ($request, $response, $args) {
        $ctrl = new PerfilesController($GLOBALS['db']);
        return $ctrl->edit($request, $response, $args);
    });

    $group->post('/perfiles/{id}/update', function ($request, $response, $args) {
        $ctrl = new PerfilesController($GLOBALS['db']);
        return $ctrl->update($request, $response, $args);
    });

    $group->post('/perfiles/{id}/delete', function ($request, $response, $args) {
        $ctrl = new PerfilesController($GLOBALS['db']);
        return $ctrl->delete($request, $response, $args);
    });

    // ── Permisos ──────────────────────────────────────────────────
    $group->get('/permisos', function ($request, $response) {
        $ctrl = new PermisosController($GLOBALS['db']);
        return $ctrl->index($request, $response);
    });

    $group->get('/permisos/{id}/edit', function ($request, $response, $args) {
        $ctrl = new PermisosController($GLOBALS['db']);
        return $ctrl->edit($request, $response, $args);
    });

    $group->post('/permisos/{id}/update', function ($request, $response, $args) {
        $ctrl = new PermisosController($GLOBALS['db']);
        return $ctrl->update($request, $response, $args);
    });

    // ── Usuarios ──────────────────────────────────────────────────
    $group->get('/usuarios', function ($request, $response) {
        $usuarios = $GLOBALS['db']->pdo->query("
            SELECT u.*, p.nombre AS perfil_nombre
            FROM users u
            LEFT JOIN perfiles p ON p.id = u.perfil_id
            ORDER BY u.name ASC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        return renderView($response, __DIR__ . '/../src/Views/Usuarios/index.php', 'Usuarios', [
            'usuarios' => $usuarios
        ]);
    });

    $group->get('/usuarios/create', function ($request, $response) {
        $perfiles = $GLOBALS['db']->select('perfiles', '*', [
            'activo' => 1,
            'ORDER'  => ['nombre' => 'ASC']
        ]);
        return renderView($response, __DIR__ . '/../src/Views/Usuarios/create.php', 'Nuevo Usuario', [
            'perfiles' => $perfiles ?: []
        ]);
    });

    $group->post('/usuarios/store', function ($request, $response) {
        $data = $request->getParsedBody();

        if ($GLOBALS['db']->has("users", ["email" => $data['email']])) {
            $_SESSION['errors'] = ["El correo ya está registrado."];
            return $response->withHeader('Location', '/usuarios/create')->withStatus(302);
        }

        $GLOBALS['db']->insert("users", [
            "name"      => $data['nombre'],
            "email"     => $data['email'],
            "password"  => password_hash($data['password'], PASSWORD_DEFAULT),
            "rol"       => $data['rol'],
            "perfil_id" => !empty($data['perfil_id']) ? (int)$data['perfil_id'] : null,
        ]);

        $_SESSION['success'] = "Usuario {$data['nombre']} creado correctamente.";
        return $response->withHeader('Location', '/usuarios')->withStatus(302);
    });

    $group->get('/usuarios/{id}/edit', function ($request, $response, $args) {
        $usuario = $GLOBALS['db']->get("users", "*", ["id" => (int)$args['id']]);
        if (!$usuario) {
            return $response->withHeader('Location', '/usuarios')->withStatus(302);
        }
        $perfiles = $GLOBALS['db']->select('perfiles', '*', [
            'activo' => 1,
            'ORDER'  => ['nombre' => 'ASC']
        ]);
        return renderView($response, __DIR__ . '/../src/Views/Usuarios/edit.php', 'Editar Usuario', [
            'usuario' => $usuario,
            'perfiles' => $perfiles ?: [],
        ]);
    });

    $group->post('/usuarios/{id}/update', function ($request, $response, $args) {
        $id   = (int)$args['id'];
        $data = $request->getParsedBody();

        if ($GLOBALS['db']->has("users", ["email" => $data['email'], "id[!]" => $id])) {
            $_SESSION['errors'] = ["El correo ya está en uso por otro usuario."];
            return $response->withHeader('Location', '/usuarios/' . $id . '/edit')->withStatus(302);
        }

        $campos = [
            "name"      => $data['nombre'],
            "email"     => $data['email'],
            "rol"       => $data['rol'],
            "perfil_id" => !empty($data['perfil_id']) ? (int)$data['perfil_id'] : null,
        ];
        if (!empty($data['password'])) {
            $campos["password"] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $GLOBALS['db']->update("users", $campos, ["id" => $id]);
        $_SESSION['success'] = "Usuario actualizado correctamente.";
        return $response->withHeader('Location', '/usuarios')->withStatus(302);
    });

    $group->post('/usuarios/{id}/delete', function ($request, $response, $args) {
        $GLOBALS['db']->delete("users", ["id" => (int)$args['id']]);
        $_SESSION['success'] = "Usuario eliminado.";
        return $response->withHeader('Location', '/usuarios')->withStatus(302);
    });
})->add($authMiddleware);

$app->run();
