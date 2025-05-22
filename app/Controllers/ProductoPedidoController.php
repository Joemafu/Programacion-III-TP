<?php

require_once __DIR__ . '/../Models/ProductoPedido.php';
require_once __DIR__ . '/../Interfaces/IApiUsable.php';

class ProductoPedidoController
{    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        $estado = $parametros['estado'];
        $tiempoEstimado = $parametros['tiempoEstimado'];

        $rol = $request->getHeaderLine('Rol');
        $idEmpleado = $request->getHeaderLine('Id');

        if (ProductoPedido::SetearAtributos($id, $estado, $tiempoEstimado, $rol, $idEmpleado))
        {
            Usuario::incrementarOperaciones($idEmpleado);
            $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
        }
        else 
        {
            $payload = json_encode(array("mensaje" => "No se pudo actualizar el estado del pedido."));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarPorId($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        if(ProductoPedido::deletePorId($id))
        {
            $payload = json_encode(array("Ok" => "Producto eliminado del pedido."));
        }
        else
        {
            $payload = json_encode(array("Error" => "No se pudo eliminar el producto del pedido. ID Incorrecto."));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}