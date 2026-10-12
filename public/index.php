<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CuentaController;
use App\Controllers\InicioController;
use App\Controllers\RegistroController;
use App\Controllers\ViveroController;
use App\Core\Config;
use App\Core\HttpNotFoundException;
use App\Core\Logger;
use App\Core\Router;
use App\Core\Session;
use App\Core\View;

require dirname(__DIR__) . '/app/core/autoload.php';

Config::load(dirname(__DIR__) . '/config/config.php');

require dirname(__DIR__) . '/app/core/helpers.php';

Logger::init(path('logs'));

date_default_timezone_set('America/Mexico_City');

$debug = (bool) Config::get('app.debug', false);

error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('display_startup_errors', $debug ? '1' : '0');

Session::start();

$router = new Router();

// Rutas de inicio
$router->get('', [InicioController::class, 'index']);

// Rutas de cuenta y autenticación
$router->get('cuenta', [CuentaController::class, 'mostrar']);
$router->post('cuenta/entrar', [AuthController::class, 'entrar']);
$router->post('cuenta/salir', [AuthController::class, 'salir']);
$router->get('cuenta/cambiar-pin', [CuentaController::class, 'mostrarCambioPin']);
$router->post('cuenta/cambiar-pin', [CuentaController::class, 'cambiarPin']);
$router->get('cuenta/olvide-pin', [AuthController::class, 'olvidePin']);

// Rutas de registro
$router->get('registro', [RegistroController::class, 'elegirTipo']);
$router->get('registro/comprador', [RegistroController::class, 'mostrarComprador']);
$router->post('registro/comprador', [RegistroController::class, 'crearComprador']);
$router->get('registro/vivero', [RegistroController::class, 'mostrarVivero']);
$router->post('registro/vivero', [RegistroController::class, 'crearVivero']);

// Rutas del panel del vivero
$router->get('panel/vivero', [ViveroController::class, 'mostrar']);
$router->post('panel/vivero/datos', [ViveroController::class, 'actualizarDatos']);
$router->post('panel/vivero/inventario/guardar', [ViveroController::class, 'guardarInventario']);
$router->post('panel/vivero/inventario/eliminar', [ViveroController::class, 'eliminarInventario']);
$router->post('panel/vivero/fotos/subir', [ViveroController::class, 'subirFoto']);
$router->post('panel/vivero/fotos/eliminar', [ViveroController::class, 'eliminarFoto']);

try {
    $router->dispatch();
} catch (HttpNotFoundException $exception) {
    http_response_code(404);
    echo View::render('errors/404', [
        'mensaje' => $exception->getMessage(),
        'debug' => $debug,
    ]);
} catch (\Throwable $exception) {
    http_response_code(500);
    echo View::render('errors/500', [
        'excepcion' => $exception,
        'debug' => $debug,
    ]);
}
