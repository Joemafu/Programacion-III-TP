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
                    $consulta = $objAccesoDatos->prepararConsulta("SELECT p.*, pp.id AS 'ID producto pedido',
                        pp.idPedido AS 'ID pedido', 
                        pp.idProducto AS 'ID producto', 
                        pp.idEmpleado AS 'ID empleado', 
                        pp.cantidad AS 'Cantidad', 
                        pp.valorSubtotal AS 'Valor subtotal',
                        pp.estado AS 'Estado', 
                        pp.tiempoEstimado AS 'Tiempo estimado del producto', 
                        pp.entregadoATiempo AS 'Entregado a tiempo', 
                        pp.rolPreparador AS 'Rol del preparador',
                        pr.nombre AS 'Nombre del producto'
                        FROM pedidos AS p 
                        LEFT JOIN productopedidos AS pp ON p.id = pp.idPedido 
                        LEFT JOIN productos AS pr ON pp.idProducto = pr.id");
                    $consulta->execute();

                    $pedidos = [];
                    while ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
                        $idPedido = $fila['id'];
                        if (!isset($pedidos[$idPedido])) {
                            $pedidos[$idPedido] = [
                                "ID" => $fila['id'],
                                "Nombre del cliente" => $fila['nombreCliente'],
                                "Estado" => $fila['estado'],
                                "Tiempo estimado" => $fila['tiempoEstimado'],
                                "Foto" => $fila['foto'],
                                "Codigo de seguimiento" => $fila['codigoSeguimiento'],
                                "ID de la mesa" => $fila['idMesa'],
                                "Puntuacion de la mesa" => $fila['puntuacionMesa'],
                                "Puntuacion del restaurante" => $fila['puntuacionRestaurante'],
                                "Puntuacion del mozo" => $fila['puntuacionMozo'],
                                "Puntuacion del cocinero" => $fila['puntuacionCocinero'],
                                "Resenia" => $fila['resenia'],
                                "Valor total" => $fila['valorTotal'],
                                "Fecha" => $fila['fecha'],
                                "Entregado a tiempo" => $fila['entregadoATiempo'],
                                "Productos" => []
                            ];
                        }

                        $producto = [
                            "Nombre del producto" => $fila['Nombre del producto'],
                            "ID producto pedido" => $fila['ID producto pedido'],
                            "ID pedido" => $fila['ID pedido'],
                            "ID Producto" => $fila['ID producto'],
                            "ID empleado" => $fila['ID empleado'],
                            "Cantidad" => $fila['Cantidad'],
                            "Valor subtotal" => $fila['Valor subtotal'],
                            "Estado" => $fila['Estado'],
                            "Tiempo estimado del producto" => $fila['Tiempo estimado del producto'],
                            "Entregado a tiempo" => $fila['Entregado a tiempo'],
                            "Rol del Preparador" => $fila['Rol del preparador']
                        ];
                        $pedidos[$idPedido]['Productos'][] = $producto;
                    }

                    return $pedidos;
                }
            case "mozo":
                {
                    $consulta = $objAccesoDatos->prepararConsulta('SELECT p.id, 
                    p.nombreCliente, 
                    p.tiempoEstimado, 
                    p.foto, 
                    p.codigoSeguimiento, 
                    p.idMesa, 
                    p.valorTotal, 
                    p.fecha, 
                    p.entregadoATiempo, 
                    pp.cantidad, 
                    pp.estado, 
                    pp.tiempoEstimado AS `Tiempo estimado del producto`, 
                    pr.nombre AS `Nombre del producto` 
                    FROM pedidos AS p 
                    LEFT JOIN productopedidos AS pp ON p.id = pp.idPedido 
                    LEFT JOIN productos AS pr ON pp.idProducto = pr.id 
                    WHERE p.estado != :estado');
                    $consulta->bindValue(':estado', "cobrado", PDO::PARAM_STR);
                    $consulta->execute();

                    $pedidos = [];
                    while ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
                        $idPedido = $fila['id'];
                        if (!isset($pedidos[$idPedido])) {
                            $pedidos[$idPedido] = [
                                "ID" => $fila['id'],
                                "Nombre del cliente" => $fila['nombreCliente'],
                                "Tiempo estimado" => $fila['tiempoEstimado'],
                                "Foto" => $fila['foto'],
                                "Codigo de seguimiento" => $fila['codigoSeguimiento'],
                                "ID de la mesa" => $fila['idMesa'],
                                "Valor total" => $fila['valorTotal'],
                                "Fecha" => $fila['fecha'],
                                "Entregado a tiempo" => $fila['entregadoATiempo'],
                                "Productos" => []
                            ];
                        }

                        $producto = [
                            "Nombre del producto" => $fila['Nombre del producto'],
                            "cantidad" => $fila['cantidad'],
                            "estado" => $fila['estado'],
                            "Tiempo estimado del producto" => $fila['Tiempo estimado del producto']
                        ];
                        $pedidos[$idPedido]['Productos'][] = $producto;
                    }

                    return $pedidos;
                }
            default:
                {
                    $consulta = $objAccesoDatos->prepararConsulta("SELECT p.nombre, pp.id, pp.idPedido, pp.idProducto, pp.idEmpleado, pp.cantidad, pp.estado, pp.tiempoEstimado
                        FROM productopedidos pp 
                        LEFT JOIN productos p ON pp.idProducto = p.id 
                        WHERE pp.estado !=:estado AND pp.rolPreparador = :rolPreparador AND (pp.idEmpleado = :idEmpleado OR pp.idEmpleado IS NULL)");
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

    // Esta es con PUT pero llega dañada.
    public function AgregarFotoB64($id, $foto)
    {
        if(Pedido::pedidoExiste($id))
        {
            $fotoBinaria = base64_decode($foto);

            $nombreArchivo = "/".$id.".jpg";

            $directorioDestino = '../FotosClientes';

            if (!is_dir($directorioDestino)) {
                mkdir($directorioDestino, 0777, true);
            }

            $rutaDestino = $directorioDestino . $nombreArchivo;

            if (file_put_contents($rutaDestino, $fotoBinaria)) 
            {
                $objAccesoDatos = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDatos->prepararConsulta('UPDATE pedidos SET foto = :foto WHERE id = :id');
                $consulta->bindValue(':foto', $rutaDestino, PDO::PARAM_STR);
                $consulta->bindValue(':id', $id, PDO::PARAM_INT);
                $consulta->execute();

                return true;

            } else 
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    // ESTE FUNCIONA CON POST
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

    public static function pedidoExisteByCodigoSeguimiento($codigoSeguimiento)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) FROM pedidos WHERE codigoSeguimiento = :codigoSeguimiento");
        $consulta->bindValue(':codigoSeguimiento', $codigoSeguimiento, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchColumn() > 0;
    }

    public static function completarResenia($codigoSeguimiento, $puntuacionMesa, $puntuacionRestaurante, $puntuacionMozo, $puntuacionCocinero, $resenia)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('UPDATE pedidos 
        SET puntuacionMesa = :puntuacionMesa, puntuacionRestaurante = :puntuacionRestaurante, puntuacionMozo = :puntuacionMozo, puntuacionCocinero = :puntuacionCocinero, resenia = :resenia 
        WHERE codigoSeguimiento = :codigoSeguimiento');
        $consulta->bindValue(':codigoSeguimiento', $codigoSeguimiento, PDO::PARAM_STR);
        $consulta->bindValue(':puntuacionMesa', $puntuacionMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionRestaurante', $puntuacionRestaurante, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionMozo', $puntuacionMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacionCocinero', $puntuacionCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':resenia', $resenia, PDO::PARAM_STR);
        $consulta->execute();

        $numFilasAfectadas = $consulta->rowCount();

        return $numFilasAfectadas > 0;
    }

    public static function obtenerMejoresComentarios()
    {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT resenia 
            FROM pedidos 
            ORDER BY puntuacionRestaurante + puntuacionMesa + puntuacionMozo + puntuacionCocinero DESC 
            LIMIT 5');
        $consulta->execute();

        $comentarios = $consulta->fetchAll(PDO::FETCH_COLUMN, 0);

        $mejoresComentarios = [];
        foreach ($comentarios as $key => $comentario) {
            $mejoresComentarios["top " . ($key + 1)] = $comentario;
        }

        return $mejoresComentarios;
    }

    public static function obtenerEntregadosTarde()
    {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT p.*, pp.id AS 'ID producto pedido',
            pp.idPedido AS 'ID pedido', 
            pp.idProducto AS 'ID producto', 
            pp.idEmpleado AS 'ID empleado', 
            pp.cantidad AS 'Cantidad', 
            pp.valorSubtotal AS 'Valor subtotal',
            pp.estado AS 'Estado', 
            pp.tiempoEstimado AS 'Tiempo estimado del producto', 
            pp.entregadoATiempo AS 'Entregado a tiempo', 
            pp.rolPreparador AS 'Rol del preparador',
            pr.nombre AS 'Nombre del producto'
            FROM pedidos AS p 
            LEFT JOIN productopedidos AS pp ON p.id = pp.idPedido 
            LEFT JOIN productos AS pr ON pp.idProducto = pr.id
            WHERE p.entregadoATiempo = :entregadoATiempo");
        $consulta->bindValue(':entregadoATiempo', "no", PDO::PARAM_STR);
        $consulta->execute();

        $pedidos = [];
        while ($fila = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $idPedido = $fila['id'];
            if (!isset($pedidos[$idPedido])) {
                $pedidos[$idPedido] = [
                    "ID" => $fila['id'],
                    "Nombre del cliente" => $fila['nombreCliente'],
                    "Estado" => $fila['estado'],
                    "Tiempo estimado" => $fila['tiempoEstimado'],
                    "Foto" => $fila['foto'],
                    "Codigo de seguimiento" => $fila['codigoSeguimiento'],
                    "ID de la mesa" => $fila['idMesa'],
                    "Puntuacion de la mesa" => $fila['puntuacionMesa'],
                    "Puntuacion del restaurante" => $fila['puntuacionRestaurante'],
                    "Puntuacion del mozo" => $fila['puntuacionMozo'],
                    "Puntuacion del cocinero" => $fila['puntuacionCocinero'],
                    "Resenia" => $fila['resenia'],
                    "Valor total" => $fila['valorTotal'],
                    "Fecha" => $fila['fecha'],
                    "Entregado a tiempo" => $fila['entregadoATiempo'],
                    "Productos" => []
                ];
            }

            $producto = [
                "Nombre del producto" => $fila['Nombre del producto'],
                "ID producto pedido" => $fila['ID producto pedido'],
                "ID pedido" => $fila['ID pedido'],
                "ID Producto" => $fila['ID producto'],
                "ID empleado" => $fila['ID empleado'],
                "Cantidad" => $fila['Cantidad'],
                "Valor subtotal" => $fila['Valor subtotal'],
                "Estado" => $fila['Estado'],
                "Tiempo estimado del producto" => $fila['Tiempo estimado del producto'],
                "Entregado a tiempo" => $fila['Entregado a tiempo'],
                "Rol del Preparador" => $fila['Rol del preparador']
            ];
            $pedidos[$idPedido]['Productos'][] = $producto;
        }

        return $pedidos;
    }

    public static function deletePorId($id)
    {
        Pedido::LiberarMesaPorIdPedido($id);
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        $numFilasAfectadas = $consulta->rowCount();

        ProductoPedido::deleteCascadaPorIdPedido($id);
    
        return $numFilasAfectadas > 0;
    }

    public static function LiberarMesaPorIdPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT idMesa FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $idMesa = $consulta->fetchColumn();

        if ($idMesa !== false) {
            Mesa::ActualizarEstado($idMesa, "disponible");
            return true;
        }

        return false;
    }

}