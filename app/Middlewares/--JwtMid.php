<?php

declare(strict_types=1);

namespace App\Middlewares;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;
use Firebase\JWT\JWT;

class JwtMid
{
    public function checkToken(string $token): object
    {
        try {
            return JWT::decode($token, $_SERVER['SECRET_KEY'], ['HS256']);
        } catch (\UnexpectedValueException) {
            
        }
    }

    public function __invoke($request, $response,$next) 
    {
        $jwtHeader = $request->getHeaderLine('Authorization');
        if (! $jwtHeader) {
            throw new Exception("Se requiere token", 400);
        }
        $jwt = explode('Bearer ', $jwtHeader);
        if (! isset($jwt[1])) {
            throw new Exception("Token inválido", 400);
        }
        $decoded = $this->checkToken($jwt[1]);
        $object = (array) $request->getParsedBody();
        $object['decoded'] = $decoded;

        return $next($request->withParsedBody($object), $response);
    }

    function generarToken($userId, $rol) {
        $secretKey = 'UTNFRA2023#';
        $fechaCreacion = time();
        $expiracion = strtotime('+1 hour', $fechaCreacion);
    
        $payload = [
            'userId' => $userId,
            'fechaCreacion' => $fechaCreacion,
            'expiracion' => $expiracion,
            'rol' => $rol
        ];
    
        return JWT::encode($payload, $secretKey, 'HS256');
    }
}