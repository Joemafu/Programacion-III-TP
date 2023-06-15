<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Firebase\JWT\JWT;
use Exception;

class JwtMiddleware
{
    private $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $token = $this->getTokenFromRequest($request);

        if (!$token) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Token not provided']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        try {
            $decodedToken = JWT::decode($token, $this->secretKey, ['HS256']);
            $request = $request->withAttribute('jwtPayload', (array) $decodedToken);
        } catch (Exception $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Invalid token']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $response = $handler->handle($request);

        return $response;
    }

    private function getTokenFromRequest(Request $request): ?string
    {
        $authorizationHeader = $request->getHeaderLine('Authorization');

        if (empty($authorizationHeader)) {
            return null;
        }

        $headerParts = explode(' ', $authorizationHeader);

        if (count($headerParts) !== 2 || $headerParts[0] !== 'Bearer') {
            return null;
        }

        return $headerParts[1];
    }
}
