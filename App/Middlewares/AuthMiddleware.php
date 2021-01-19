<?php

namespace App\Middlewares;

use App\DAO\Database\UserDAO;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class AuthMiddleware
{

    public function isAdmin(Request $request, Response $response, $next)
    {
        $userDAO = new UserDAO();

        $token = $request->getHeader('TOKEN');

        $user = $userDAO->getUserByToken($token);

        if (!isset($user)) { //verificar se o utilizador existe associado ao respetivo token
            $response->getBody()->write(json_encode("Unauthenticated"), JSON_UNESCAPED_UNICODE);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        if ($userDAO->isAdmin($user['id'])) {
            $response = $next($request, $response);
            return $response;
        }

        $response->getBody()->write(json_encode("Unauthorized"), JSON_UNESCAPED_UNICODE);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(403);
    }

    public function isAtleta(Request $request, Response $response, $next)
    {
        $userDAO = new UserDAO();

        $token = $request->getHeader('TOKEN');

        $user = $userDAO->getUserByToken($token);

        if (!isset($user)) { //verificar se o utilizador existe associado ao respetivo token
            $response->getBody()->write(json_encode("Unauthenticated"), JSON_UNESCAPED_UNICODE);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        if ($userDAO->isAtleta($user['id'])) {
            $response = $next($request, $response);
            return $response;
        }

        $response->getBody()->write(json_encode("Unauthorized"), JSON_UNESCAPED_UNICODE);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(403);
    }

    public function isTreinador(Request $request, Response $response, $next)
    {
        $userDAO = new UserDAO();

        $token = $request->getHeader('TOKEN');

        $user = $userDAO->getUserByToken($token);

        if (!isset($user)) { //verificar se o utilizador existe associado ao respetivo token
            $response->getBody()->write(json_encode("Unauthenticated"), JSON_UNESCAPED_UNICODE);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        if ($userDAO->isTreinador($user['id']) || $userDAO->isAdmin($user['id'])) {
            $response = $next($request, $response);
            return $response;
        }

        $response->getBody()->write(json_encode("Unauthorized"), JSON_UNESCAPED_UNICODE);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(403);
    }


    public function headers(Request $request, Response $response, $next)
    {
        $response = $next($request, $response);
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, token, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    }
}
