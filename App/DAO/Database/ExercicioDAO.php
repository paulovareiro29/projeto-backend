<?php

namespace App\DAO\Database;

class ExercicioDAO extends Connection {

    public function __construct(){
        parent::__construct();
    }

    public function getAll(){
        $data = array();

        foreach ($this->db->exercicio() as $exercicio){
                $tipoExercicio_id = $exercicio['tipoExercicio_id'];

                array_push($data,[
                    'id' => $exercicio['id'],
                    'nome' => $exercicio['nome'],
                    'descricao' => $exercicio['descricao'],
                    'tipo' => isset($tipoExercicio_id) ? $this->getTipoExercicio($tipoExercicio_id) : null,
                ]);
        }


        return $data;
    }

    public function get($id){
        $data = array();

        foreach ($this->db->exercicio()->where('id',$id) as $exercicio){
            $tipoExercicio_id = $exercicio['tipoExercicio_id'];

               return [
                   'id' => $exercicio['id'],
                   'nome' => $exercicio['nome'],
                   'descricao' => $exercicio['descricao'],
                   'tipo' => isset($tipoExercicio_id) ? $this->getTipoExercicio($tipoExercicio_id) : null,
               ];
        }

        return $data;
    }

    public function insert($data): bool{
        $id = $this->db->exercicio()->insert($data);

        if(!$id) return false;

        return true;
    }

    public function update($data): bool{
        $exercicio = $this->db->exercicio()->where('id', $data['id']);

        if(!$exercicio) return false;

        $exercicio->update($data);

        return true;
    }

    public function delete($id){

        $this->db->blocoexercicio()->where('exercicio_id', $id)->delete();

        $result = $this->db->exercicio()->where('id',$id)->delete();

        return $result;
    }

    public function getAllTipos(){
        $data = array();

        foreach ($this->db->tipoexercicio() as $tipo){

               array_push($data,[
                   'id' => $tipo['id'],
                   'nome' => $tipo['nome'],
                   'descricao' => $tipo['descricao'],
               ]);
        }


        return $data;
    }

    public function getTipoExercicio($id){
        return $this->db->tipoexercicio[$id];
    }

    public function insertTipoExercicio($data): bool{
        $id = $this->db->tipoexercicio()->insert($data);

        if(!$id) return false;

        return true;
    }

    public function updateTipoExercicio($data): bool{
        $exercicio = $this->db->tipoexercicio()->where('id', $data['id']);

        if(!$exercicio) return false;

        $exercicio->update($data);

        return true;
    }

    public function deleteTipoExercicio($id): bool{
        $this->db->exercicio()->where('tipoExercicio_id', $id)->update(['tipoExercicio_id' => null]);

        $result = $this->db->tipoexercicio()->where('id',$id)->delete();

        return $result;
    }

}