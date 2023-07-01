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

    public function crearProductoPedido()
    {
        if (!Producto::productoExisteById($this->idProducto)) {
            return false;
        }

        $this->rolPreparador = Producto::getRolById($this->idProducto);

        $this->valorSubtotal = (int)$this->cantidad * (double)Producto::getPrecioById($this->idProducto);

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('INSERT INTO productopedidos (idPedido, idProducto, cantidad, rolPreparador, valorSubtotal) VALUES (:idPedido,:idProducto,:cantidad, :rolPreparador, :valorSubtotal)');
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_STR);
        $consulta->bindValue(':rolPreparador', $this->rolPreparador, PDO::PARAM_STR);
        $consulta->bindValue(':valorSubtotal', $this->valorSubtotal, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    // public static function obtenerTodos()
    // {
    //     $objAccesoDatos = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, precio, tipo FROM productos");
    //     $consulta->execute();


    //     return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    // }
}