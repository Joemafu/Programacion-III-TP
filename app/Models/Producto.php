<?php
class Producto
{
    public $id;
    public $nombre;
    public $precio;
    public $tipo;

    public function __construct()
    {
        
    }

    public function crearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('INSERT INTO productos (nombre, precio, tipo) VALUES (:nombre,:precio,:tipo)');
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, precio, tipo FROM productos");
        $consulta->execute();


        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }
}