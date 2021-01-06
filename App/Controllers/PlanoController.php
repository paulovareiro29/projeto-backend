<?php

namespace App\Controllers;

use App\DAO\Database\PlanoDAO;
use App\Models\PlanoModel;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class PlanoController {

    public function index(Request $request, Response $response, array $args): Response {
        $planoDAO = new PlanoDAO();


        $planos = $planoDAO->getAllPlanos();

        $response->getBody()->write(json_encode($planos) , JSON_UNESCAPED_UNICODE);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function show(Request $request, Response $response, array $args): Response {
        $planoDAO = new PlanoDAO();

        $id = $request->getAttribute('id');

        $plano = $planoDAO->getPlano($id);

        $response->getBody()->write(json_encode($plano) , JSON_UNESCAPED_UNICODE);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function create(Request $request, Response $response, array $args): Response {
        $planoDAO = new PlanoDAO();

        $body = $request->getParsedBody(); 

        $fields = array();

        foreach(PlanoModel::getFields() as $field){ 
            if(!isset($body[$field]) || $body[$field] == ""){
                $fields[$field] = null;
                continue;
            }
            $fields[$field] = $body[$field];
        }

        $result = $planoDAO->insert($fields); 

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
        $planoDAO = new PlanoDAO();

        $body = $request->getParsedBody(); 

        $fields = array();
        $fields['id'] = $request->getAttribute('id');

        foreach(PlanoModel::getFields() as $field){ 
            if(!isset($body[$field]) || $body[$field] == ""){
                $fields[$field] = null;
                continue;
            }
            $fields[$field] = $body[$field];
        }

        $result = $planoDAO->update($fields); 

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

    public function delete(Request $request, Response $response, array $args): Response {
        $planoDAO = new PlanoDAO();

        $id = $request->getAttribute('id');

        $result =  $planoDAO->delete($id);
        

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
        $planoDAO = new PlanoDAO();

        $body = $request->getParsedBody(); 

        $result = $planoDAO->associate($body);

        if($result){
            $response->getBody()->write(json_encode("Plano associado a atleta com sucesso") , JSON_UNESCAPED_UNICODE);
            return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
        }
            
        
        $response->getBody()->write(json_encode("Erro ao associar plano e atleta") , JSON_UNESCAPED_UNICODE);
        return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
    }

    public function addExercise(Request $request, Response $response, array $args): Response {
        $planoDAO = new PlanoDAO();

        $body = $request->getParsedBody();

        $id = $request->getAttribute('id');

        $dia = (!isset($body['dia']) || $body['dia'] == "") ? null : $body['dia'];
        
        if(!isset($dia))
            return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        

        $fields = array();
        
        $bloco = $planoDAO->getBloco([
            'plano_id' => $id, 
            'dia' => $dia]);
            
        $fields['bloco_id'] = $bloco;       
        $fields['realizado'] = false;

        foreach(PlanoModel::getFieldsBlocoExercicio() as $field){ 
            if(!isset($body[$field]) || $body[$field] == ""){
                $fields[$field] = null;
                continue;
            }
            $fields[$field] = $body[$field];
        }

        $result = $planoDAO->addExercise($fields);

        if($result){
            $response->getBody()->write(json_encode("Exercicio adicionado ao plano com sucesso") , JSON_UNESCAPED_UNICODE);
            return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
        }
            
        
        $response->getBody()->write(json_encode("Erro ao adicionar exercicio ao plano") , JSON_UNESCAPED_UNICODE);
        return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);

    }

    public function showBloco(Request $request, Response $response, array $args): Response {
        $planoDAO = new PlanoDAO();

        $plano_id = $request->getAttribute('id');
        $dia = $request->getAttribute('dia');
        
        $bloco = $planoDAO->getBloco([
            'plano_id' => $plano_id, 
            'dia' => $dia]);

        $result = $planoDAO->showBloco($bloco);

        $response->getBody()->write(json_encode($result) , JSON_UNESCAPED_UNICODE);
        return $response 
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
    }

    public function planosAtleta(Request $request, Response $response, array $args): Response {
        $planoDAO = new PlanoDAO();

        $id = $request->getAttribute('id');

        $planos = $planoDAO->getPlanosAtleta($id);

        $response->getBody()->write(json_encode($planos) , JSON_UNESCAPED_UNICODE);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
        
    }

    public function planosTreinador(Request $request, Response $response, array $args): Response {
        $planoDAO = new PlanoDAO();

        $id = $request->getAttribute('id');

        $planos = $planoDAO->getPlanosTreinador($id);

        $response->getBody()->write(json_encode($planos) , JSON_UNESCAPED_UNICODE);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    }

    public function atletasAssociadosPlano(Request $request, Response $response, array $args): Response {
        $planoDAO = new PlanoDAO();

        $id = $request->getAttribute('id');

        $atletas = $planoDAO->getAtletasAssociados($id);

        $response->getBody()->write(json_encode($atletas) , JSON_UNESCAPED_UNICODE);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);

    }
}