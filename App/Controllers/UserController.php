<?php

namespace App\Controllers;

use App\DAO\Database\UserDAO;
use App\Models\UserModel;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class UserController {
    
    public function index(Request $request, Response $response, array $args): Response {
        $userDAO = new UserDAO();
        $users = $userDAO->getAllUsers();

        $response->getBody()->write(json_encode($users) , JSON_UNESCAPED_UNICODE);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function show(Request $request, Response $response, array $args): Response {
        $userDAO = new UserDAO();
        $id = $request->getAttribute('id');

        $user = $userDAO->getUser($id);

        $response->getBody()->write(json_encode($user) , JSON_UNESCAPED_UNICODE);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function updateByToken(Request $request, Response $response, array $args): Response {
        $userDAO = new UserDAO();

        $body = $request->getParsedBody(); 

        $token = $request->getAttribute('token');

        $user = $userDAO->getUserByToken($token);

        if(isset($user)){
            $fields = array(); //array dos campos do utilizador
            $roles = array(); //array das roles do utilizador

            $fields['id'] = $user['id'];

            //loop para se a role for vazia atribuir false
            foreach(UserModel::getRoles() as $role){ 
                if(!isset($body[$role])) $body[$role] = 'false';
                $roles[$role] = ($body[$role] == 'true'? true : false);
            }
    
            //loop para se algum campo do user for vazio atribuir null
            foreach(UserModel::getFields() as $field){ 
                if(!isset($body[$field]) || $body[$field] == ""){
                    //$fields[$field] = null;
                    continue;
                }
                $fields[$field] = $body[$field];
            }

            $result = $userDAO->update($fields, $roles);

            if($result){
                $response->getBody()->write(json_encode("Registo atualizado com sucesso") , JSON_UNESCAPED_UNICODE);
                return $response 
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            }
                
            
            $response->getBody()->write(json_encode("Erro ao atualizar registo") , JSON_UNESCAPED_UNICODE);
            return $response 
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
        }

        $response->getBody()->write(json_encode("Erro ao atualizar registo") , JSON_UNESCAPED_UNICODE);
            return $response 
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(400);
    }

    public function getByToken(Request $request, Response $response, array $args): Response {
        $userDAO = new UserDAO();
        $token = $request->getAttribute('token');

        $user = $userDAO->getUserByToken($token);

        $response->getBody()->write(json_encode($user) , JSON_UNESCAPED_UNICODE);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function login(Request $request, Response $response, array $args): Response {
        $userDAO = new UserDAO();
        $body = $request->getParsedBody();

        $token = $userDAO->login($body);

        $response->getBody()->write(json_encode(['token' => $token]) , JSON_UNESCAPED_UNICODE);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function create(Request $request, Response $response, array $args): Response {
        $userDAO = new UserDAO();

        $body = $request->getParsedBody(); 

        $fields = array(); //array dos campos do utilizador
        $roles = array(); //array das roles do utilizador

        //loop para se a role for vazia atribuir false
        foreach(UserModel::getRoles() as $role){ 
            if(!isset($body[$role])) $body[$role] = 'false';
            $roles[$role] = ($body[$role] == 'true'? true : false);
        }

        //loop para se algum campo do user for vazio atribuir null
        foreach(UserModel::getFields() as $field){ 
            if(!isset($body[$field]) || $body[$field] == ""){
                $fields[$field] = null;
                continue;
            }
            $fields[$field] = $body[$field];
        }

        //inserir o utilizador
        $result = $userDAO->insert($fields, $roles); 

        if($result){
            $response->getBody()->write(json_encode("Registo inserido com sucesso") , JSON_UNESCAPED_UNICODE);
            return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
        }
            
        
        $response->getBody()->write(json_encode("Erro ao inserir registo") , JSON_UNESCAPED_UNICODE);
        return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
          
    }

    public function update(Request $request, Response $response, array $args): Response {
        $userDAO = new UserDAO();

        $body = $request->getParsedBody(); 

        $fields = array(); //array dos campos do utilizador
        $roles = array(); //array das roles do utilizador

        $fields['id'] = $request->getAttribute('id');

        //loop para se a role for vazia atribuir false
        foreach(UserModel::getRoles() as $role){ 
            if(!isset($body[$role])) continue;
            $roles[$role] = ($body[$role] == 'true'? true : false);
        }

        //loop para se algum campo do user for vazio atribuir null
        foreach(UserModel::getFields() as $field){ 
            if(!isset($body[$field]) || $body[$field] == ""){
                //s$fields[$field] = null;
                continue;
            }
            $fields[$field] = $body[$field];
        }


        $result = $userDAO->update($fields, $roles);

        if($result){
            $response->getBody()->write(json_encode("Registo atualizado com sucesso") , JSON_UNESCAPED_UNICODE);
            return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }
            
        
        $response->getBody()->write(json_encode("Erro ao atualizar registo") , JSON_UNESCAPED_UNICODE);
        return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $userDAO = new UserDAO();  
        $id = $request->getAttribute('id');

        $result =  $userDAO->delete($id);


        if($result){
            $response->getBody()->write(json_encode("Registo eliminado com sucesso") , JSON_UNESCAPED_UNICODE);
            return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }
            
        
        $response->getBody()->write(json_encode("Erro ao eliminar registo") , JSON_UNESCAPED_UNICODE);
        return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);

    }

    public function associate(Request $request, Response $response, array $args): Response {
        $userDAO = new UserDAO();

        $body = $request->getParsedBody(); 

        $result = $userDAO->associate($body);

        if($result){
            $response->getBody()->write(json_encode("Atleta e treinador associado com sucesso") , JSON_UNESCAPED_UNICODE);
            return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
        }
            
        
        $response->getBody()->write(json_encode("Erro ao associar atleta e treinador") , JSON_UNESCAPED_UNICODE);
        return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
    }

    public function dissociate(Request $request, Response $response, array $args): Response {
        $userDAO = new UserDAO();

        $body = $request->getParsedBody(); 

        $result = $userDAO->dissociate($body);

        if($result){
            $response->getBody()->write(json_encode("Atleta e treinador desassociados com sucesso") , JSON_UNESCAPED_UNICODE);
            return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
        }
            
        
        $response->getBody()->write(json_encode("Erro ao desassociar atleta e treinador") , JSON_UNESCAPED_UNICODE);
        return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
    }
}