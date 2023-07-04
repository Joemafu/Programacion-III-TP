<?php
class Producto
{
    public $id;
    public $nombre;
    public $precio;
    public $tipo;
    public $contadorVendidos;
    

    public function __construct()
    {
        
    }

    public function crearProducto()
    {
        if ($this->productoExisteByName()) {
            return false;
        }

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('INSERT INTO productos (nombre, precio, tipo, contadorVendidos) VALUES (:nombre,:precio,:tipo,:contadorVendidos)');
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':contadorVendidos', $this->contadorVendidos, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos");
        $consulta->execute();


        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    private function productoExisteByName()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) FROM productos WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchColumn() > 0;
    }

    public static function productoExisteById($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) FROM productos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchColumn() > 0;
    }

    public static function productosExisten($arrayProductos)
    {
        foreach ($arrayProductos as $producto)
        {
            if(!Producto::productoExisteById($producto['idProducto']))
            {
                return false;
            }
        }
        return true;
    }

    public static function getRolById($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT tipo FROM productos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        $tipo=$consulta->fetchColumn();

        switch($tipo)
        {
            case ("trago"):
            {
                return "bartender";
            }
            case ("cerveza"):
            {
                return "cervecero";
            }
            default:
            {
                return "cocinero";
            }
        }
    }

    public static function getPrecioById($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT precio FROM productos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return (double)$consulta->fetchColumn();
    }

    public static function incrementarProductoVendido($id, $cantidad)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('UPDATE productos SET contadorVendidos = contadorVendidos + :cantidad WHERE id = :id');
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function deletePorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM productos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        $numFilasAfectadas = $consulta->rowCount();
    
        return $numFilasAfectadas > 0;
    }
}