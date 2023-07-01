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
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (idMesa, nombreCliente, estado, codigoSeguimiento, fecha) VALUES (:idMesa, :nombreCliente, :estado, :codigoSeguimiento, :fecha)");
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':estado', 'en preparacion', PDO::PARAM_STR);
        $consulta->bindValue(':codigoSeguimiento', $this->codigoSeguimiento, PDO::PARAM_STR);
        $consulta->bindvalue(':fecha', date('Y-m-d'), PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos($rol)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        switch ($rol)
        {
            case "socio":
                {
                    $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
                    
                    $consulta->execute();

                    return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
                }
            case "mozo":
                {
                    $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE estado = :estado");
                    $consulta->bindValue(':estado', "en preparacion", PDO::PARAM_STR);
                    $consulta->execute();
            
                    return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
                }
            default:
                {
                    echo $rol;
                    $consulta = $objAccesoDatos->prepararConsulta("SELECT id,idPedido,idProducto,idEmpleado,cantidad,estado,tiempoEstimado FROM productopedidos WHERE rolPreparador = :rolPreparador AND idEmpleado = :idEmpleado");
                    $consulta->bindValue(':rolPreparador', $rol, PDO::PARAM_STR);







                    //ACA TENGO QUE PONER EL ID






                    $consulta->bindValue(':idEmpleado', 1, PDO::PARAM_INT);
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

    private static function pedidoExiste($id)
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
}