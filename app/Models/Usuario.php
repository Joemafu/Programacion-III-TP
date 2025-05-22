<?php

class Usuario
{
    public $id;
    public $usuario;
    public $clave;
    public $rol;
    public $suspendido;
    public $contadorOperaciones;

    public function crearUsuario()
    {
        if ($this->usuarioExiste($this->usuario)) {
            return false;
        }

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave, rol, suspendido, contadorOperaciones) VALUES (:usuario, :clave, :rol, :suspendido, :contadorOperaciones)");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':rol', $this->rol, PDO::PARAM_STR);
        $consulta->bindValue(':suspendido', $this->suspendido, PDO::PARAM_BOOL);
        $consulta->bindValue(':contadorOperaciones', $this->contadorOperaciones, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, rol, suspendido, contadorOperaciones FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerTodosCSV()
    {
        $usuarios = Usuario::obtenerTodos();

        $csvData = "id,usuario,rol,suspendido,contadorOperaciones\n";
        foreach ($usuarios as $usuario) {
            $csvData .= $usuario->id . ',' . $usuario->usuario . ',' . $usuario->rol . ',' . $usuario->suspendido . ',' . $usuario->contadorOperaciones . "\n";
        }

        return $csvData;
    }

    private function usuarioExiste($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchColumn() > 0;
    }

    public static function incrementarOperaciones($idEmpleado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta('UPDATE usuarios SET contadorOperaciones = contadorOperaciones + 1 WHERE id = :idEmpleado');
        $consulta->bindValue(':idEmpleado', $idEmpleado, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function deletePorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM usuarios WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        $numFilasAfectadas = $consulta->rowCount();
    
        return $numFilasAfectadas > 0;
    }
}

?>