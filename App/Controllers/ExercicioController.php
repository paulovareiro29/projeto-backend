<?php

namespace App\Controllers;

use App\DAO\Database\ExercicioDAO;
use App\Models\ExercicioModel;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class ExercicioController {

    public function index(Request $request, Response $response, array $args): Response {
        $exercicioDAO = new ExercicioDAO();

        $exercicios = $exercicioDAO->getAll();

        $response->getBody()->write(json_encode($exercicios) , JSON_UNESCAPED_UNICODE);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function show(Request $request, Response $response, array $args): Response {
        $exercicioDAO = new ExercicioDAO();

        $id = $request->getAttribute('id');

        $exercicio = $exercicioDAO->get($id);

        $response->getBody()->write(json_encode($exercicio) , JSON_UNESCAPED_UNICODE);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function create(Request $request, Response $response, array $args): Response {
        $exercicioDAO = new ExercicioDAO();

        $body = $request->getParsedBody(); 

        $fields = array();

        foreach(ExercicioModel::getFields() as $field){ 
            if(!isset($body[$field]) || $body[$field] == ""){
                $fields[$field] = null;
                continue;
            }
            $fields[$field] = $body[$field];
        }

        $result = $exercicioDAO->insert($fields); 

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
        $exercicioDAO = new ExercicioDAO();

        $body = $request->getParsedBody(); 

        $fields = array();

        $fields['id'] = $request->getAttribute('id');

        foreach(ExercicioModel::getFields() as $field){ 
            if(!isset($body[$field]) || $body[$field] == ""){
                $fields[$field] = null;
                continue;
            }
            $fields[$field] = $body[$field];
        }


        $result = $exercicioDAO->update($fields); 

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
        $exercicioDAO = new ExercicioDAO();

        $id = $request->getAttribute('id');

        $result =  $exercicioDAO->delete($id);
        

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

    public function indexTipoExercicio(Request $request, Response $response, array $args): Response {
        $exercicioDAO = new ExercicioDAO();

        $exercicios = $exercicioDAO->getAllTipos();

        $response->getBody()->write(json_encode($exercicios) , JSON_UNESCAPED_UNICODE);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    }

    public function showTipoExercicio(Request $request, Response $response, array $args): Response {
        $exercicioDAO = new ExercicioDAO();

        $id = $request->getAttribute('id');

        $exercicio = $exercicioDAO->getTipoExercicio($id);

        $response->getBody()->write(json_encode($exercicio) , JSON_UNESCAPED_UNICODE);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function createTipoExercicio(Request $request, Response $response, array $args): Response {
        $exercicioDAO = new ExercicioDAO();

        $body = $request->getParsedBody(); 

        $fields = array();

        foreach(ExercicioModel::getFieldsTipoExercicio() as $field){ 
            if(!isset($body[$field]) || $body[$field] == ""){
                $fields[$field] = null;
                continue;
            }
            $fields[$field] = $body[$field];
        }

        $result = $exercicioDAO->insertTipoExercicio($fields); 

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

    public function updateTipoExercicio(Request $request, Response $response, array $args): Response {
        $exercicioDAO = new ExercicioDAO();

        $body = $request->getParsedBody(); 

        $fields = array();

        $fields['id'] = $request->getAttribute('id');

        foreach(ExercicioModel::getFieldsTipoExercicio() as $field){ 
            if(!isset($body[$field]) || $body[$field] == ""){
                $fields[$field] = null;
                continue;
            }
            $fields[$field] = $body[$field];
        }

        $result = $exercicioDAO->updateTipoExercicio($fields); 

        if($result){
            $response->getBody()->write(json_encode("Registo atualizado com sucesso") , JSON_UNESCAPED_UNICODE);
            return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
        }
            
        
        $response->getBody()->write(json_encode("Erro ao atualizar registo") , JSON_UNESCAPED_UNICODE);
        return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
    }

    public function deleteTipoExercicio(Request $request, Response $response, array $args): Response {
        $exercicioDAO = new ExercicioDAO();

        $id = $request->getAttribute('id');

        $result =  $exercicioDAO->deleteTipoExercicio($id);
        

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
}