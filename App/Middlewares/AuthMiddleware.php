<?php

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class AuthMiddleware {

    public function isAdmin(Request $request, Response $response, $next){
        //POR FAZER
        $response = $next($request,$response);

        return $response;
    }

}