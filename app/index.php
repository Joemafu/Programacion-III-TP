<?php

/*
    1er Sprint (13/06)
      ❖ Dar de alta y listar usuarios(mozo, bartender...)
      ❖ Dar de alta y listar productos(bebidas y comidas)
      ❖ Dar de alta y listar mesas
      ❖ Dar de alta y listar pedidos
    2do Sprint (20/06)
      ❖ Usar MW de usuarios/perfiles
      ❖ Verificar usuarios para las tareas de abm
      ❖ Manejo del estado del pedido
    3er Sprint (27/06)
      ❖ Carga de datos desde un archivo .CSV
      ❖ Descarga de archivos .CSV
*/

// Establecer el nivel de error a -1 para mostrar todos los errores
error_reporting(-1);
// Habilitar la visualización de errores en pantalla
ini_set('display_errors', 1);

// Importar la interfaz ResponseInterface del estándar PSR-7 y asignarla como Response
use Psr\Http\Message\ResponseInterface as Response;
// Importar la interfaz ServerRequestInterface del estándar PSR-7 y asignarla como Request
use Psr\Http\Message\ServerRequestInterface as Request;
// Importar la interfaz RequestHandlerInterface del estándar PSR-15 y asignarla como RequestHandler
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
// Importar la clase Response de Slim PSR-7 y asignarla como ResponseMW para diferenciarla de la interfaz Response
use Slim\Psr7\Response as ResponseMW;
// Importar la clase AppFactory de Slim Factory
use Slim\Factory\AppFactory;
// Importar la clase RouteCollectorProxy de Slim Routing
use Slim\Routing\RouteCollectorProxy;
// Importar Middleware JWT
use App\Middlewares\JwtTokenValidatorMiddleware;

// Requerir el archivo autoload.php para cargar las dependencias
require __DIR__ . '/../vendor/autoload.php';
require_once './Controllers/MesaController.php';
require_once './Controllers/PedidoController.php';
require_once './Controllers/ProductoController.php';
require_once './Controllers/UsuarioController.php';
require_once './db/AccesoDatos.php';
require_once './Middlewares/LoginMiddleware.php';
require_once './Middlewares/JwtTokenGeneratorMiddleware.php';
require_once './Middlewares/JwtTokenValidatorMiddleware.php';

// Crear una instancia de la aplicación Slim
$app = AppFactory::create();

// Agregar el middleware de manejo de errores
$app->addErrorMiddleware(true, true, true);

// Agregar el middleware de análisis del cuerpo de la solicitud
$app->addBodyParsingMiddleware();

// Defino las rutas dentro del grupo '/usuarios'
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    // Ruta para el verbo POST
    $group->post('[/]', \UsuarioController ::class . ':CargarUno')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
    // Ruta para el verbo GET
    $group->get('[/]', \UsuarioController ::class . ':TraerTodos')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));

    // Subruta para el verbo POST (CSV)
    $group->post('/csv[/]', \UsuarioController ::class . ':CargarDesdeCSV')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
    // Subruta para el verbo GET (CSV)
    $group->get('/csv[/]', \UsuarioController ::class . ':DescargarTodosCSV');//->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
});

// Defino las rutas dentro del grupo '/productos'
$app->group('/productos', function (RouteCollectorProxy $group) {
    // Ruta para el verbo POST
    $group->post('[/]', \ProductoController ::class . ':CargarUno')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
    // Ruta para el verbo GET
    $group->get('[/]', \ProductoController ::class . ':TraerTodos')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo","bartender","cervecero","cocinero"]));
});
  
// Defino las rutas dentro del grupo '/mesas'
$app->group('/mesas', function (RouteCollectorProxy $group) {
  // Ruta para el verbo POST
  $group->post('[/]', \MesaController ::class . ':CargarUno')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio"]));
  // Ruta para el verbo GET
  $group->get('[/]', \MesaController ::class . ':TraerTodos')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo"]));
});
  
// Defino las rutas dentro del grupo '/pedidos'
$app->group('/pedidos', function (RouteCollectorProxy $group) {
  // Ruta para el verbo POST
  $group->post('[/]', \PedidoController ::class . ':CargarUno')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo"]));
  // Ruta para el verbo GET
  $group->get('[/]', \PedidoController ::class . ':TraerTodos')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo","bartender","cervecero","cocinero"]));
  // Ruta para el verbo PUT
  $group->put('[/]', \PedidoController ::class . ':ModificarUno')->add(new JwtTokenValidatorMiddleware("UTNFRA2023#", ["socio", "mozo","bartender","cervecero","cocinero"]));
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

// Para terminar: 
/*

  asociar las tareas y vistas

  conectar mesas y pedidos

*/


// Socio token Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJyb2wiOiJzb2NpbyJ9.Zx6xV70vP4GyDNuXK4ktceFc6mM7rWRZXUoJdOfkx0A

?>