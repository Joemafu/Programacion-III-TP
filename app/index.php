<?php

error_reporting(-1);
ini_set('display_errors', 1);

use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use App\Middlewares\JwtTokenValidatorMiddleware;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Controllers/MesaController.php';
require_once __DIR__ . '/Controllers/PDFController.php';
require_once __DIR__ . '/Controllers/PedidoController.php';
require_once __DIR__ . '/Controllers/ProductoController.php';
require_once __DIR__ . '/Controllers/ProductoPedidoController.php';
require_once __DIR__ . '/Controllers/UsuarioController.php';
require_once __DIR__ . '/db/AccesoDatos.php';
require_once __DIR__ . '/Middlewares/LoginMiddleware.php';
require_once __DIR__ . '/Middlewares/JwtTokenGeneratorMiddleware.php';
require_once __DIR__ . '/Middlewares/JwtTokenValidatorMiddleware.php';

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->post('[/]', \UsuarioController ::class . ':CargarUno')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
    $group->get('[/]', \UsuarioController ::class . ':TraerTodos')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
    $group->post('/csv[/]', \UsuarioController ::class . ':CargarDesdeCSV')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
    $group->get('/csv[/]', \UsuarioController ::class . ':DescargarTodosCSV');//->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
    $group->delete('/borrar', \UsuarioController ::class . ':BorrarPorId')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
});

$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->post('[/]', \ProductoController ::class . ':CargarUno')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
    $group->get('[/]', \ProductoController ::class . ':TraerTodos')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo","bartender","cervecero","cocinero"]));
    $group->delete('/borrar', \ProductoController ::class . ':BorrarPorId')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
});

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->post('[/]', \MesaController ::class . ':CargarUno')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
  $group->get('[/]', \MesaController ::class . ':TraerTodos')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo"]));
  $group->get('/lamasusada', \MesaController ::class . ':TraerMesaMasUsada')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
  $group->delete('/borrar', \MesaController ::class . ':BorrarPorId')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
});

$app->put('/cerrarmesa[/]', \MesaController ::class . ':CerrarMesa')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->post('[/]', \PedidoController ::class . ':CargarUno')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo"]));
  $group->get('[/]', \PedidoController ::class . ':TraerTodos')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo","bartender","cervecero","cocinero"]));
  $group->put('[/]', \PedidoController ::class . ':ModificarUno')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo"]));
  $group->post('/subirfoto[/]', \PedidoController ::class . ':SubirFoto')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo"]));
  $group->get('/consultartiempoestimado[/]', \PedidoController ::class . ':GetTiempoEstimado');
  $group->get('/mejorescomentarios[/]', \PedidoController ::class . ':GetMejoresComentarios')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
  $group->get('/entregadostarde[/]', \PedidoController ::class . ':GetEntregadosTarde')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
  $group->delete('/borrar', \PedidoController ::class . ':BorrarPorId')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
});

$app->put('/subirfoto[/]', \PedidoController ::class . ':SubirFotoB64')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo"]));
$app->put('/encuesta[/]', \PedidoController ::class .  ':CompletarEncuesta');
$app->put('/servirpedido[/]', \PedidoController ::class . ':ServirPedido')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo"]));
$app->put('/cobrarpedido[/]', \PedidoController ::class . ':CobrarPedido')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo"]));

$app->get('/pdf[/]', \PDFController ::class . ':GetPDF');

$app->group('/productopedidos', function (RouteCollectorProxy $group) {
  $group->put('[/]', \ProductoPedidoController ::class . ':ModificarUno')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio","bartender","cervecero","cocinero"]));
  $group->delete('/borrar', \ProductoPedidoController ::class . ':BorrarPorId')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
});

$app->post('/login', function ($request, $response, $args) {
  return $response;
})
->add(new JwtTokenGeneratorMiddleware("UTNFRA2023#"))
->add(new LoginMiddleware())
;

$app->run();

// composer update
// php -S localhost:666 router.php

/*
  Delete de todas las entidades
  Web Hosting
 */

?>