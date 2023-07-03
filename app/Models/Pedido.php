<?php

class Pedido
{
    public $id;
    public $idMesa;
    public $nombreCliente;
    public $estado;
    public $tiempoEstimado;
    public $foto;
    public $codigoSeguimiento;
    public $puntuacionMesa;
    public $puntuacionRestaurante;
    public $puntuacionMozo;
    public $puntuacionCocinero;
    public $resenia;
    public $valorTotal;
    public $fecha;
    public $entregadoATiempo;

    public function __construct()
    {
        
    }

    function crearPedido()
    {
        $this->codigoSeguimiento = Pedido::generarCodigoSeguimiento();

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (idMesa, nombreCliente, estado, tiempoEstimado, codigoSeguimiento, fecha) 
                                                                VALUES (:idMesa, :nombreCliente, :estado, :tiempoEstimado, :codigoSeguimiento, :fecha)");
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':estado', 'en preparacion', PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEstimado', $this->tiempoEstimado, PDO::PARAM_INT);
        $consulta->bindValue(':codigoSeguimiento', $this->codigoSeguimiento, PDO::PARAM_STR);
        $consulta->bindvalue(':fecha', date('Y-m-d'), PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos($rol,$id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        switch ($rol)
        {
            case "socio":
                {
                    $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
                    
                    $consulta->execute();

                    return $consulta->fetchAll(PDO::FETCH_ASSOC);
                }
            case "mozo":
                {
                    $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE estado != :estado");
                    $consulta->bindValue(':estado', "cobrado", PDO::PARAM_STR);
                    $consulta->execute();
            
                    return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
                }
            default:
                {
                    $consulta = $objAccesoDatos->prepararConsulta("SELECT p.nombre, pp.id, pp.idPedido, pp.idProducto, pp.idEmpleado, pp.cantidad, pp.estado, pp.tiempoEstimado
                        FROM productopedidos pp 
                        LEFT JOIN productos p ON pp.idProducto = p.id 
                        WHERE pp.estado != :estado AND pp.rolPreparador = :rolPreparador AND (pp.idEmpleado = :idEmpleado OR pp.idEmpleado IS NULL)");
                    $consulta->bindValue(':rolPreparador', $rol, PDO::PARAM_STR);
                    $consulta->bindValue(':estado', "servido", PDO::PARAM_STR);
                    $consulta->bindValue(':idEmpleado', $id, PDO::PARAM_INT);
                    $consulta->execute();
            
                    return $consulta->fetchAll(PDO::FETCH_ASSOC);
                }
        }
    }

    public static function ActualizarEstado($id, $estado) : bool {

        try {
            if(Pedido::pedidoExiste($id))
            {
                $objAccesoDatos = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDatos->prepararConsulta('UPDATE pedidos SET estado = :estado WHERE id = :id');
                $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
                $consulta->bindValue(':id', $id, PDO::PARAM_INT);
                $consulta->execute();
            }
            else
            {
                return false;
            }            
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public static function setSeEntregoATiempo($id,$entregadoATiempo)
    {
        try {
            if(Pedido::pedidoExiste($id))
            {
                $objAccesoDatos = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDatos->prepararConsulta('UPDATE pedidos SET entregadoATiempo = :entregadoATiempo WHERE id = :id');
                $consulta->bindValue(':entregadoATiempo', $entregadoATiempo, PDO::PARAM_STR);
                $consulta->bindValue(':id', $id, PDO::PARAM_INT);
                $consulta->execute();
            }
            else
            {
                return false;
            }            
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public static function pedidoExiste($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchColumn() > 0;
    }

    private static function generarCodigoSeguimiento() {
        $codigo = uniqid(); 
        $codigo = strtoupper($codigo); 
        $codigo = str_replace(".", "", $codigo); 
        $codigo = substr($codigo, -5); 
        
        return $codigo;
    }

    public function AgregarFoto($id, $foto)
    {
        if(Pedido::pedidoExiste($id))
        {
            $nombreArchivo = "/".$id.".jpg";

            $directorioDestino = '../FotosClientes';

            if (!is_dir($directorioDestino)) {
                mkdir($directorioDestino, 0777, true);
            }
            $rutaDestino = $directorioDestino . $nombreArchivo;
            $foto->moveTo($rutaDestino);

            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta('UPDATE pedidos SET foto = :foto WHERE id = :id');
            $consulta->bindValue(':foto', $rutaDestino, PDO::PARAM_STR);
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            $consulta->execute();

            return true;
        }
        else
        {
            return false;
        }
    }

    public static function getMesaByIdPediddo($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT idMesa FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        $resultado = $consulta->fetchColumn();

        return $resultado;
    }

    public static function obtenerTiempoEstimado($codigoSeguimiento)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT tiempoEstimado FROM pedidos WHERE codigoSeguimiento = :codigoSeguimiento");
        $consulta->bindValue(':codigoSeguimiento', $codigoSeguimiento, PDO::PARAM_STR);
        $consulta->execute();

        $resultado = $consulta->fetchColumn();

        return $resultado;
    }

    public static function ActualizarTiempoEstimado($tiempoEstimado, $idProductoPedido)
    {
        try
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();

            $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos
                INNER JOIN productopedidos ON pedidos.id = productopedidos.idPedido
                SET pedidos.tiempoEstimado = :tiempoEstimado
                WHERE productopedidos.id = :idProductoPedido
                AND pedidos.tiempoEstimado < :nuevoTiempoEstimado
            ");
    
            $consulta->bindValue(':tiempoEstimado', $tiempoEstimado, PDO::PARAM_INT);
            $consulta->bindValue(':idProductoPedido', $idProductoPedido, PDO::PARAM_INT);
            $consulta->bindValue(':nuevoTiempoEstimado', $tiempoEstimado, PDO::PARAM_INT);
            $consulta->execute();
    
            $numFilasAfectadas = $consulta->rowCount();
    
            return $numFilasAfectadas > 0;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return false;
        }        
    }

    public static function CalcularTotalPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos
            INNER JOIN (
                SELECT idPedido, SUM(valorSubtotal) AS totalSubtotal
                FROM productopedidos
                GROUP BY idPedido
            ) AS subquery ON pedidos.id = subquery.idPedido
            SET pedidos.valorTotal = subquery.totalSubtotal
            WHERE pedidos.id = :id
        ");

        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        $numFilasAfectadas = $consulta->rowCount();

        return $numFilasAfectadas > 0;
    }
}