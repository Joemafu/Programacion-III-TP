<?php
class ProductoPedido
{
    public $id;
    public $idPedido;
    public $idProducto;
    public $idEmpleado;
    public $cantidad;
    public $valorSubtotal;
    public $estado;
    public $tiempoEstimado;
    public $entregadoATiempo;
    public $rolPreparador;
    

    public function __construct()
    {
        
    }

    // public function crearProducto()
    // {
    //     if ($this->productoExiste($this->nombre)) {
    //         return false;
    //     }

    //     $objAccesoDatos = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDatos->prepararConsulta('INSERT INTO productos (nombre, precio, tipo) VALUES (:nombre,:precio,:tipo)');
    //     $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
    //     $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
    //     $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
    //     $consulta->execute();

    //     return $objAccesoDatos->obtenerUltimoId();
    // }

    // public static function obtenerTodos()
    // {
    //     $objAccesoDatos = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, precio, tipo FROM productos");
    //     $consulta->execute();


    //     return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    // }

    // private function productoExiste($nombre)
    // {
    //     $objAccesoDatos = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) FROM productos WHERE nombre = :nombre");
    //     $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
    //     $consulta->execute();

    //     return $consulta->fetchColumn() > 0;
    // }
}