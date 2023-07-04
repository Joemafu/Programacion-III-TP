<?php
require_once './Models/Mesa.php';
require_once './Interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $estado = $parametros['estado'];

        $mesa = new Mesa();
        $mesa->estado=$estado;
        $mesa->contadorClientes=0;
        $mesa->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("Lista de mesas" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CerrarMesa($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        
        if(Mesa::ActualizarEstado($id, "cerrado"))
        {
            $payload = json_encode(array("OK" => "Mesa ID ".$id." cerrada."));
        }
        else
        {
            $payload = json_encode(array("Error" => "Mesa ID ".$id." no existe o ya se encuentra cerrada."));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerMesaMasUsada($request, $response, $args)
    {
        $mesaMasUsada = Mesa::obtenerMesaMasUsada();

        $payload = json_encode($mesaMasUsada);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarPorId($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        if(Mesa::deletePorId($id))
        {
            $payload = json_encode(array("Ok" => "Mesa eliminado."));
        }
        else
        {
            $payload = json_encode(array("Error" => "No se pudo eliminar la Mesa. ID Incorrecto."));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}