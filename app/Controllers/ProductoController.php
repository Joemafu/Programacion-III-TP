<?php

require_once __DIR__ . '/../Models/Producto.php';
require_once __DIR__ . '/../Interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $tipo = $parametros['tipo'];
        $contadorVendidos = 0;

        $producto = new Producto();
        $producto->nombre = $nombre;
        $producto->precio = $precio;
        $producto->tipo = $tipo;
        $producto->contadorVendidos = $contadorVendidos;

        if ($producto->crearProducto()!==false)
        {
            $payload = json_encode(array("mensaje" => "Producto creado con exito"));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "El producto no pudo darse de alta porque ya existe."));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("Lista de productos" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarPorId($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        if(Producto::deletePorId($id))
        {
            $payload = json_encode(array("Ok" => "Producto eliminado."));
        }
        else
        {
            $payload = json_encode(array("Error" => "No se pudo eliminar el producto. ID Incorrecto."));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}