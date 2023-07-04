<?php

class Mesa
{
    public $id;
    public $estado;
    public $contadorClientes;

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (estado, contadorClientes) VALUES (:estado, :contadorClientes)");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':contadorClientes', $this->contadorClientes, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function ActualizarEstado($id, $estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('UPDATE mesas SET estado = :estado WHERE id = :id');
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        $filasAfectadas = $consulta->rowCount();

        return $filasAfectadas > 0;
    } 

    public static function incrementarContadorClientes($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('UPDATE mesas SET contadorClientes = contadorClientes + 1 WHERE id = :id');
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function GetEstado($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT estado FROM mesas WHERE id = :id');
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchColumn();
    } 

    public static function obtenerMesaMasUsada()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('SELECT id, contadorClientes
            FROM mesas 
            ORDER BY contadorClientes DESC 
            LIMIT 1');
        $consulta->execute();

        $mesaMasUsada = $consulta->fetch(PDO::FETCH_ASSOC);

        $retorno = "La mesa mas usada es la mesa ID " . $mesaMasUsada['id']." con " . $mesaMasUsada["contadorClientes"]. " clientes.";
        return $retorno;
    }
}