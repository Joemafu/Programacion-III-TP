<?php

require_once __DIR__ . '/../Models/Pedido.php';
require_once __DIR__ . '/../Models/ProductoPedido.php';
require_once __DIR__ . '/../Interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $idMesa = $parametros['idMesa'];
        $nombreCliente = $parametros['nombreCliente'];
        $productosPedidos = $parametros['productos'];

        $idEmpleado = $request->getHeaderLine('Id');

        if(Mesa::GetEstado($idMesa)!=="disponible")
        {
            $payload = json_encode(array("Error" => "La mesa no se encuentra disponible."));
        }
        else if(Producto::productosExisten($productosPedidos))
        {
            $pedido = new Pedido();
            $pedido->idMesa = $idMesa;
            $pedido->nombreCliente = $nombreCliente;
            $pedido->tiempoEstimado = 0;
            
            $idPedido = $pedido->crearPedido();

            Mesa::ActualizarEstado($idMesa,"con cliente esperando");

            foreach ($productosPedidos as $producto)
            {
                
                $productoPedido = new ProductoPedido();
                $productoPedido->idPedido= $idPedido;
                $productoPedido->idProducto = $producto['idProducto'];
                $productoPedido->cantidad = $producto['cantidad'];
                $productoPedido->crearProductoPedido();
                
                Producto::incrementarProductoVendido($productoPedido->idProducto, $productoPedido->cantidad);
            }

            Pedido::CalcularTotalPedido($idPedido);

            Usuario::incrementarOperaciones($idEmpleado);

            $payload = json_encode(array("Pedido cargado, codigo de seguimiento: ".$pedido->codigoSeguimiento => "ID: ".$idPedido));
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
        $id = $request->getHeaderLine('Id');

        $lista = Pedido::obtenerTodos($rol, $id);
        $payload = json_encode(array("Lista de pedidos" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
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

    //Esta es para put pero llega dañada.
    public function SubirFotoB64($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $idPedido = $parametros['idPedido'];
        $foto = $parametros['foto'];

       

        if(Pedido::AgregarFotoB64($idPedido, $foto))
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

    // ESTA FUNCIONA CON POST
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

    public function GetTiempoEstimado($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        $codigoSeguimiento = $parametros['nroPedido'];

        $resultado = Pedido::obtenerTiempoEstimado($codigoSeguimiento);

        if ($resultado!==false)
        {
            $payload = json_encode(array("Tiempo estimado" => $resultado." minutos."));
        }
        else
        {
            $payload = json_encode(array("Error" => "El codigo de seguimiento no existe."));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');   
    }

    public function ServirPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        $entregadoATiempo = $parametros['entregadoATiempo'];

        if(Pedido::pedidoExiste($id))
        {
            Pedido::ActualizarEstado($id,"servido");
            Pedido::setSeEntregoATiempo($id, $entregadoATiempo);
            ProductoPedido::ActualizarEstado($id,"servido");
            ProductoPedido::setSeEntregoATiempo($id,$entregadoATiempo);
            $idMesa = Pedido::getMesaByIdPediddo($id);            
            Mesa::ActualizarEstado($idMesa, "con cliente comiendo");
            $payload = json_encode(array("Ok" => "Se sirvió el pedido ID ".$id));
        }
        else{
            $payload = json_encode(array("Error" => "El pedido ID ".$id. " no existe."));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CobrarPedido($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];

        if(Pedido::pedidoExiste($id))
        {
            $idMesa = Pedido::getMesaByIdPediddo($id);

            Mesa::incrementarContadorClientes($idMesa);
            Mesa::ActualizarEstado($idMesa, "disponible");
            Pedido::ActualizarEstado($id, "cobrado");

            $payload = json_encode(array("Ok" => "Se actualizaron los estados correspondientes."));
        }
        else
        {
            $payload = json_encode(array("Error" => "El pedido ID ".$id. " no existe."));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CompletarEncuesta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $codigoSeguimiento = $parametros['codigoSeguimiento'];
        $puntuacionMesa = $parametros['puntuacionMesa'];
        $puntuacionRestaurante = $parametros['puntuacionRestaurante'];
        $puntuacionMozo = $parametros['puntuacionMozo'];
        $puntuacionCocinero = $parametros['puntuacionCocinero'];
        $resenia = $parametros['resenia'];

        if(Pedido::pedidoExisteByCodigoSeguimiento($codigoSeguimiento))
        {
            if(Pedido::completarResenia($codigoSeguimiento, $puntuacionMesa, $puntuacionRestaurante, $puntuacionMozo, $puntuacionCocinero, $resenia))
            {
                $payload = json_encode(array("Ok" => "Se enviaron tus comentarios, gracias por participar!."));
            }
            else
            {
                $payload = json_encode(array("Error" => "La reseña enviada es igual a una previamente recibida."));
            }
        }
        else
        {
            $payload = json_encode(array("Error" => "El codigo de seguimiento ".$codigoSeguimiento. " es inválido."));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function GetMejoresComentarios($request, $response, $args)
    {
        $comentarios = Pedido::obtenerMejoresComentarios();

        $payload = json_encode($comentarios);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function GetEntregadosTarde($request, $response, $args)
    {
        $entregadosTarde = Pedido::obtenerEntregadosTarde();

        $payload = json_encode($entregadosTarde);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarPorId($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        if(Pedido::deletePorId($id))
        {
            $payload = json_encode(array("Ok" => "Pedido eliminado."));
        }
        else
        {
            $payload = json_encode(array("Error" => "No se pudo eliminar el Pedido. ID Incorrecto."));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}