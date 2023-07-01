<?php

use function PHPSTORM_META\type;

require_once './Models/Pedido.php';
require_once './Models/ProductoPedido.php';
require_once './Interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $productoInexistente=false;
        $parametros = $request->getParsedBody();

        $idMesa = $parametros['idMesa'];
        $nombreCliente = $parametros['nombreCliente'];
        $productosPedidos = $parametros['productos'];

        if(Mesa::GetEstado($idMesa)!=="disponible")
        {
            $payload = json_encode(array("Error" => "La mesa no se encuentra disponible."));
        }
        else if(Producto::productosExisten($productosPedidos))
        {
            $pedido = new Pedido();
            $pedido->idMesa = $idMesa;
            $pedido->nombreCliente = $nombreCliente;
            
            $idPedido = $pedido->crearPedido();

            Mesa::ActualizarEstado($idMesa,"con cliente esperando");

            foreach ($productosPedidos as $producto)
            {
                $productoPedido = new ProductoPedido();
                $productoPedido->idPedido= $idPedido;
                $productoPedido->idProducto = $producto['idProducto'];
                $productoPedido->cantidad = $producto['cantidad'];

                $productoPedido->crearProductoPedido();
            }
            $payload = json_encode(array("Pedido cargado, codigo de seguimiento: " => $pedido->codigoSeguimiento));
        }
        else
        {
            $payload = json_encode(array("Error" => "Uno o mas de los productos pedidos no existe."));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $rol = $request->getHeaderLine('Rol');

        $lista = Pedido::obtenerTodos($rol);
        $payload = json_encode(array("Lista de pedidos" => $lista));

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


    public function SubirFoto($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $idPedido = $parametros['idPedido'];

        $uploadedFiles = $request->getUploadedFiles();
        $foto = $uploadedFiles['foto'];

        if(Pedido::AgregarFoto($idPedido, $foto))
        {
            $payload = json_encode(array("Ok" => "Se agrego la foto al pedido ID ".$idPedido));
        }
        else
        {
            $payload = json_encode(array("Error" => "El ID de pedido no existe."));
        }    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');    
    }





}