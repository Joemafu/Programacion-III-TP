<?php

require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

use Psr\Http\Message\UploadedFileInterface;

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $rol = $parametros['rol'];

        $usr = new Usuario();
        $usr->usuario=$usuario;
        $usr->clave=$clave;
        $usr->rol=$rol;
        if ($usr->crearUsuario()!==false)
        {
            $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
        }
        else
        {
            $payload = json_encode(array("mensaje" => "El usuario no pudo crearse porque ya existe."));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuarios" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function DescargarTodosCSV($request, $response)
    {
        $usuariosCSV = Usuario::obtenerTodosCSV();

        $response = $response->withHeader('Content-Type', 'text/csv')
        ->withHeader('Content-Disposition', 'attachment; filename="usuarios.csv"');
        $response->getBody()->write($usuariosCSV);
        return $response;
    }

    public function CargarDesdeCSV($request, $response)
    {
        $uploadedFiles = $request->getUploadedFiles();
        $archivoCSV = $uploadedFiles['usuariosCSV'];
        $repetidos = 0;
        $nuevos = 0;

        if ($archivoCSV->getError() === UPLOAD_ERR_OK) {

            // Obtener el contenido del archivo CSV
            $contenidoCsv = file_get_contents($archivoCSV->getStream()->getMetadata('uri'));

            if (!empty($contenidoCsv))
            {
                $lineas = explode(PHP_EOL, $contenidoCsv);

                foreach ($lineas as $linea) {
                    $linea = trim($linea);

                    if (!empty($linea)) {
                        $campos = explode(",", $linea);

                        $usuario = $campos[0];
                        $clave = $campos[1];
                        $rol = $campos[2];

                        $usr = new Usuario();
                        $usr->usuario=$usuario;
                        $usr->clave=$clave;
                        $usr->rol=$rol;

                        if($usr->crearUsuario()===false)
                        {
                            $repetidos++;
                        }
                        else 
                        {
                            $nuevos++;
                        }
                    }
                }
            }
            else
            {
                $response->withStatus(400)->getBody()->write("Error el archivo CSV está vacío");
            }

            $response->withStatus(200)->getBody()->write("Carga desde CSV completada, se cargaron ".$nuevos." usuarios nuevos. Se omitieron ".$repetidos." usuarios ya existentes.");
        } 
        else 
        {
            $response->withStatus(400)->getBody()->write("Error al cargar el archivo CSV");
        }   
        return $response;
    }

    // public function TraerUno($request, $response, $args)
    // {
    //     // Buscamos usuario por nombre
    //     $usr = $args['usuario'];
    //     $usuario = Usuario::obtenerUsuario($usr);
    //     $payload = json_encode($usuario);

    //     $response->getBody()->write($payload);
    //     return $response
    //       ->withHeader('Content-Type', 'application/json');
    // }
    
    // public function ModificarUno($request, $response, $args)
    // {
    //     $parametros = $request->getParsedBody();

    //     $nombre = $parametros['nombre'];
    //     Usuario::modificarUsuario($nombre);

    //     $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

    //     $response->getBody()->write($payload);
    //     return $response
    //       ->withHeader('Content-Type', 'application/json');
    // }

    // public function BorrarUno($request, $response, $args)
    // {
    //     $parametros = $request->getParsedBody();

    //     $usuarioId = $parametros['usuarioId'];
    //     Usuario::borrarUsuario($usuarioId);

    //     $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

    //     $response->getBody()->write($payload);
    //     return $response
    //       ->withHeader('Content-Type', 'application/json');
    // }
}