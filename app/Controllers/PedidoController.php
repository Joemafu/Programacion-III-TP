<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombreCliente = $parametros['nombreCliente'];
        $estado = $parametros['estado'];
        $tiempoEstimado = $parametros['tiempoEstimado'];
        $foto = $parametros['foto'];

        // Creamos el pedido
        $pedido = new Pedido();
        $pedido->nombreCliente=$nombreCliente;
        $pedido->estado=$estado;
        $pedido->tiempoEstimado=$tiempoEstimado;
        $pedido->foto=$foto;
        
        $pedido->crearPedido();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

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

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    // public function ModificarUno($request, $response, $args)
    // {
    //     $parametros = $request->getParsedBody();

    //     $nombre = $parametros['nombre'];
    //     Pedido::modificarPedido($nombre);

    //     $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

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