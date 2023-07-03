<?php

error_reporting(-1);
ini_set('display_errors', 1);

use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use App\Middlewares\JwtTokenValidatorMiddleware;

require __DIR__ . '/../vendor/autoload.php';
require_once './Controllers/MesaController.php';
require_once './Controllers/PedidoController.php';
require_once './Controllers/ProductoController.php';
require_once './Controllers/ProductoPedidoController.php';
require_once './Controllers/UsuarioController.php';
require_once './db/AccesoDatos.php';
require_once './Middlewares/LoginMiddleware.php';
require_once './Middlewares/JwtTokenGeneratorMiddleware.php';
require_once './Middlewares/JwtTokenValidatorMiddleware.php';

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->post('[/]', \UsuarioController ::class . ':CargarUno')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
    $group->get('[/]', \UsuarioController ::class . ':TraerTodos')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
    $group->post('/csv[/]', \UsuarioController ::class . ':CargarDesdeCSV')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
    $group->get('/csv[/]', \UsuarioController ::class . ':DescargarTodosCSV');//->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
});

$app->group('/productos', function (RouteCollectorProxy $group) {
    $group->post('[/]', \ProductoController ::class . ':CargarUno')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
    $group->get('[/]', \ProductoController ::class . ':TraerTodos')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo","bartender","cervecero","cocinero"]));
});

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->post('[/]', \MesaController ::class . ':CargarUno')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
  $group->get('[/]', \MesaController ::class . ':TraerTodos')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo"]));
});

$app->put('/cerrarmesa[/]', \MesaController ::class . ':CerrarMesa')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->post('[/]', \PedidoController ::class . ':CargarUno')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo"]));
  $group->get('[/]', \PedidoController ::class . ':TraerTodos')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo","bartender","cervecero","cocinero"]));
  $group->put('[/]', \PedidoController ::class . ':ModificarUno')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo"]));
  $group->post('/subirfoto[/]', \PedidoController ::class . ':SubirFoto')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo"]));
  $group->get('/consultartiempoestimado[/]', \PedidoController ::class . ':GetTiempoEstimado');
});

$app->put('/servirpedido[/]', \PedidoController ::class . ':ServirPedido')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo"]));
$app->put('/cobrarpedido[/]', \PedidoController ::class . ':CobrarPedido')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo"]));

$app->group('/productopedidos', function (RouteCollectorProxy $group) {
  $group->put('[/]', \ProductoPedidoController ::class . ':ModificarUno')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio","bartender","cervecero","cocinero"]));
});

$app->post('/login', function ($request, $response, $args) {
  return $response;
})
->add(new JwtTokenGeneratorMiddleware("UTNFRA2023#"))
->add(new LoginMiddleware())
;

$app->run();

// composer update
// php -S localhost:666

// <?php echo `whoami`; 

/*
 * DE LOS PEDIDOS:
 * VER ANIDADOS LOS PRODUCTOS EN EL JSON PEDIDOS 
*/

?>