<?php


namespace App\DAO\Database;

class PlanoDAO extends Connection {

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllPlanos(): array{
        $data = array();

        foreach ($this->db->plano() as $plano){

               array_push($data,[
                   'id' => $plano['id'],
                   'treinador' => $plano['treinador_id'],
                   'nome' => $plano['nome'],
                   'descricao' => $plano['descricao'],
                   'data_inicial' => $plano['data_inicial'],
                   'data_final' => $plano['data_final']
               ]);
        }


        return $data;
    }

    public function getPlano($id){
        $data = array();

        foreach ($this->db->plano()->where('id', $id) as $plano){

               array_push($data,[
                   'id' => $plano['id'],
                   'treinador' => $plano['treinador_id'],
                   'nome' => $plano['nome'],
                   'descricao' => $plano['descricao'],
                   'data_inicial' => $plano['data_inicial'],
                   'data_final' => $plano['data_final']
               ]);
        }


        return $data;
    }

    public function insert($data): bool{
        $userDAO = new UserDAO();
        
        if(!$userDAO->isTreinador($data['treinador_id'])) return false;

        $data['treinador_id'] = $userDAO->getTreinadorByUserID($data['treinador_id'])['id'];

        $id = $this->db->plano()->insert($data);

        if(!$id) return false;

        return true;
    }

    public function update($data){
        $userDAO = new UserDAO();
        
        if(!$userDAO->isTreinador($data['treinador_id'])) return false;

        $data['treinador_id'] = $userDAO->getTreinadorByUserID($data['treinador_id'])['id'];

        $plano = $this->db->plano()->where('id', $data['id']);

        if(!$plano) return false;

        $plano->update($data);

        return true;
    }

    public function delete($id){
        //delete a todas as tabelas onde tem o id do plano
        //futuro -> {
        //    delete a blocotreino,
        //    delete a blocoExercicio
        //}

        $this->db->planoatleta()->where('plano_id', $id)->delete();

        $result = $this->db->plano()->where('id',$id)->delete();

        return $result;
    }

    public function associate($data){
        $userDAO = new UserDAO();

        $data['atleta_id'] = $userDAO->getAtletaByUserID($data['atleta_id']);

        $result = $this->db->planoatleta()->insert($data);
        return $result;
    }

    public function getPlanosAtleta($id){
        $userDAO = new UserDAO();

        if(!$userDAO->isAtleta($id)) return null;

        $data = array();

        foreach ($this->db->planoatleta()->where('atleta_id', $id) as $row){
            foreach($this->db->plano()->where('id',$row['plano_id']) as $plano){

                array_push($data,[
                    'id' => $plano['id'],
                    'treinador' => $plano['treinador_id'],
                    'nome' => $plano['nome'],
                    'descricao' => $plano['descricao'],
                    'data_inicial' => $plano['data_inicial'],
                    'data_final' => $plano['data_final']
                ]);
            }     
        }


        return $data;
    }

    public function getPlanosTreinador($id){
        $userDAO = new UserDAO();

        if(!$userDAO->isTreinador($id)) return null;

        $data = array();

        $treinador_id = $userDAO->getTreinadorByUserID($id)['id'];

        foreach($this->db->plano()->where('treinador_id',$treinador_id) as $plano){

            array_push($data,[
                'id' => $plano['id'],
                'treinador' => $plano['treinador_id'],
                'nome' => $plano['nome'],
                'descricao' => $plano['descricao'],
                'data_inicial' => $plano['data_inicial'],
                'data_final' => $plano['data_final']
            ]);
        }     


        return $data;
    } 

}