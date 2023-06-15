<?php

class Pedido
{
    public $id;
    public $nombreCliente;
    public $estado;
    public $tiempoEstimado;
    public $foto;

    public function __construct()
    {
        
    }

    function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (nombreCliente, estado, tiempoEstimado, foto) VALUES (:nombreCliente, :estado, :tiempoEstimado, :foto)");
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEstimado', $this->tiempoEstimado, PDO::PARAM_INT);
        $consulta->bindvalue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombreCliente, estado, tiempoEstimado, foto FROM pedidos");
        $consulta->execute();

        
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }
}