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
        $consulta = $objAccesoDatos->prepararConsulta('INSERT INTO productopedidos (idPedido, idProducto, cantidad, estado, rolPreparador, valorSubtotal) VALUES (:idPedido,:idProducto,:cantidad, :estado, :rolPreparador, :valorSubtotal)');
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_STR);
        $consulta->bindValue(':estado', "asignando preparador", PDO::PARAM_STR);
        $consulta->bindValue(':rolPreparador', $this->rolPreparador, PDO::PARAM_STR);
        $consulta->bindValue(':valorSubtotal', $this->valorSubtotal, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function SetearAtributos($id, $estado, $tiempoEstimado, $rol, $idEmpleado) : bool {

        try {
            if(ProductoPedido::productoPedidoExiste($id))
            {
                $objAccesoDatos = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDatos->prepararConsulta('UPDATE productopedidos SET estado = :estado, tiempoEstimado = :tiempoEstimado, idEmpleado = :idEmpleado WHERE id = :id AND rolPreparador = :rolPreparador');
                $consulta->bindValue(':id', $id, PDO::PARAM_INT);
                $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
                $consulta->bindValue(':tiempoEstimado', $tiempoEstimado, PDO::PARAM_INT);
                $consulta->bindValue(':rolPreparador', $rol, PDO::PARAM_STR);
                $consulta->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);

                Pedido::ActualizarTiempoEstimado($tiempoEstimado, $id);
                
                $consulta->execute();
                if ($consulta->rowCount() === 0) {
                    return false;
                }
            }
            else
            {
                return false;
            }            
        } catch (Exception $e)
        {
            echo $e->getMessage();
            return false;
        }

        return true;
    }

    public static function ActualizarEstado($idPedido, $estado) : bool {

        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta('UPDATE productopedidos SET estado = :estado WHERE idPedido = :idPedido');
            $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            
            $consulta->execute();
            if ($consulta->rowCount() === 0) {
                return false;
            }            
        } 
        catch (Exception $e)
        {
            echo $e->getMessage();
            return false;
        }

        return true;
    }

    public static function setSeEntregoATiempo($idPedido,$entregadoATiempo)
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta('UPDATE productopedidos SET entregadoATiempo = :entregadoATiempo WHERE idPedido = :idPedido');
            $consulta->bindValue(':entregadoATiempo', $entregadoATiempo, PDO::PARAM_STR);
            $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
            $consulta->execute();
         
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    private static function productoPedidoExiste($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) FROM productopedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchColumn() > 0;
    }

    public static function CalcularTiempoEstimado($idPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT MAX(tiempoEstimado) AS tiempoMaximo FROM productopedidos WHERE idPedido = :idPedido");
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();

        $resultado = $consulta->fetchColumn();

        return $resultado;
    }

    public static function deletePorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consultaIdPedido = $objAccesoDatos->prepararConsulta("SELECT idPedido FROM productopedidos WHERE id = :id");
        $consultaIdPedido->bindValue(':id', $id, PDO::PARAM_INT);
        $consultaIdPedido->execute();
        $idPedido = $consultaIdPedido->fetchColumn();

        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM productopedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        $numFilasAfectadas = $consulta->rowCount();

        Pedido::CalcularTotalPedido($idPedido);
    
        return $numFilasAfectadas > 0;
    }

    public static function deleteCascadaPorIdPedido($idPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM productopedidos WHERE idPedido = :idPedido");
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_INT);
        $consulta->execute();

        $numFilasAfectadas = $consulta->rowCount();
    
        return $numFilasAfectadas > 0;
    }
}