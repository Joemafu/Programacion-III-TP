<?php

use function PHPSTORM_META\type;

require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombreCliente = $parametros['nombreCliente'];
        $producto = $parametros['producto'];
        $estado = $parametros['estado'];
        $tiempoEstimado = $parametros['tiempoEstimado'];
        $uploadedFiles = $request->getUploadedFiles();
        $foto = $uploadedFiles['foto'];
        $nombreArchivo = "/".date('Y-m-d H-i-s')."hs. ".$nombreCliente.".jpg";

        $directorioDestino = '../ImagenesPedidos';

        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }
        $rutaDestino = $directorioDestino . $nombreArchivo;
        $foto->moveTo($rutaDestino);

        $pedido = new Pedido();
        $pedido->nombreCliente=$nombreCliente;
        $pedido->producto=$producto;
        $pedido->estado=$estado;
        $pedido->tiempoEstimado=$tiempoEstimado;
        $pedido->foto=$rutaDestino;
        
        $pedido->crearPedido();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        $estado = $parametros['estado'];

        if (Pedido::ActualizarEstado($id, $estado))
        {
            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
        }
        else 
        {
            $payload = json_encode(array("mensaje" => "No se pudo actualizar el estado del pedido."));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    // public function TraerUno($request, $response, $args)
    // {
    //     // Buscamos pedido por nombre
    //     $usr = $args['pedido'];
    //     $pedido = Pedido::obtenerPedido($usr);
    //     $payload = json_encode($pedido);

    //     $response->getBody()->write($payload);
    //     return $response
    //       ->withHeader('Content-Type', 'application/json');
    // }

    // public function BorrarUno($request, $response, $args)
    // {
    //     $parametros = $request->getParsedBody();

    //     $pedidoId = $parametros['pedidoId'];
    //     Pedido::borrarPedido($pedidoId);

    //     $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));

    //     $response->getBody()->write($payload);
    //     return $response
    //       ->withHeader('Content-Type', 'application/json');
    // }
}