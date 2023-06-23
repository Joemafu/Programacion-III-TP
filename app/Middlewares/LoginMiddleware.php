<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

include_once './db/AccesoDatos.php';

class LoginMiddleware
{
    public function __invoke (Request $request, RequestHandler $handler): Response {
        
        $data = $request->getParsedBody();
        $usuario = $data['usuario'] ?? '';
        $clave = $data['clave'] ?? '';

        $rol = LoginMiddleware::validarCredenciales($usuario, $clave);

        //var_dump($rol);
    
        if ($rol!==false) {


            //  pruebo pisando el parsed body

            $request->withParsedBody([$rol]);
            $response = $handler->handle($request);





            // $request = $request->withAttribute('rol', $rol);
            // var_dump($request);
            // $response = $handler->handle($request);
    
            return $response;

        } else {

            $response = new Response();
            $response->getBody()->write('Credenciales inválidas!');
            $response = $response->withStatus(401);
    
            return $response;

        }
    }
    
    static function validarCredenciales($usuario, $clave) {

        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta('SELECT * FROM usuarios WHERE usuario = :usuario AND clave = :clave');
            $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
            $consulta->execute();


            $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

            if ($usuario)
            {
                return $usuario['rol'];
            }
            else{
                return false;
            }

        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }
}