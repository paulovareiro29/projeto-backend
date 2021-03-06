<?php


namespace App\DAO\Database;

class PlanoDAO extends Connection
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllPlanos(): array
    {
        $userDAO = new UserDAO();

        $data = array();

        foreach ($this->db->plano() as $plano) {

            $from = date_create($plano['data_inicial']);
            $to = date_create($plano['data_final']);
            $days = date_diff($to, $from)->days;

            $treinador = $userDAO->getUser($userDAO->getTreinador($plano['treinador_id'])['utilizador_id']);

            array_push($data, [
                'id' => $plano['id'],
                'treinador' => $treinador,
                'nome' => $plano['nome'],
                'descricao' => $plano['descricao'],
                'data_inicial' => $plano['data_inicial'],
                'data_final' => $plano['data_final'],
                'dias' => $days,
                'atletas_associados' => $this->getAtletasAssociados($plano['id']),
                'blocos' => $this->getBlocos($plano['id'])
            ]);
        }


        return $data;
    }

    public function getBlocos($id)
    {
        $data = array();

        foreach ($this->db->bloco()->where('plano_id', $id) as $bloco) {
            array_push($data, $this->showBloco($bloco['id']));
        }

        return $data;
    }

    public function getPlano($id)
    {
        $userDAO = new UserDAO();

        foreach ($this->db->plano()->where('id', $id) as $plano) {

            $from = date_create($plano['data_inicial']);
            $to = date_create($plano['data_final']);
            $days = date_diff($to, $from)->days;

            $treinador = $userDAO->getUser($userDAO->getTreinador($plano['treinador_id'])['utilizador_id']);

            return [
                'id' => $plano['id'],
                'treinador' => $treinador,
                'nome' => $plano['nome'],
                'descricao' => $plano['descricao'],
                'data_inicial' => $plano['data_inicial'],
                'data_final' => $plano['data_final'],
                'dias' => $days,
                'atletas_associados' => $this->getAtletasAssociados($plano['id']),
                'blocos' => $this->getBlocos($plano['id'])
            ];
        }
    }

    public function insert($data)
    {
        $userDAO = new UserDAO();

        if (!$userDAO->isTreinador($data['treinador_id'])) return false;

        $data['treinador_id'] = $userDAO->getTreinadorByUserID($data['treinador_id'])['id'];

        $id = $this->db->plano()->insert($data);

        if (!$id) return false;

        $from = date_create($data['data_inicial']);
        $to = date_create($data['data_final']);
        $days = date_diff($to, $from)->days;

        for ($i = 0; $i < $days; $i++) {
            $this->db->bloco()->insert([
                'plano_id' => $id,
                'dia' => $i
            ]);
        }

        return $id;
    }

    public function update($data)
    {
        $userDAO = new UserDAO();

        $old_plano = $this->getPlano($data['id']);

        $data_inicial = null;
        $data_final = null;

        if (isset($data['data_inicial'])) {
            $data_inicial = $data['data_inicial'];
        } else {
            $data_inicial = $old_plano['data_inicial'];
        }

        if (isset($data['data_final'])) {
            $data_final = $data['data_final'];
        } else {
            $data_final = $old_plano['data_final'];
        }

        $from = date_create($data_inicial);
        $to = date_create($data_final);
        $days = date_diff($to, $from)->days;


        $qnt_bloco_existentes = $this->db->bloco()->where('plano_id', $data['id'])->count();
        if ($days > $qnt_bloco_existentes) {
            $a = null;
            for ($i = $qnt_bloco_existentes; $i < $days; $i++) {
                $this->db->bloco()->insert([
                    'plano_id' => $data['id'],
                    'dia' => $i
                ]);
            }
        }

        return $this->db->plano()->where('id',$data['id'])->update($data);

        return true;
    }

    public function delete($id)
    {

        $this->db->planoatleta()->where('plano_id', $id)->delete();

        foreach($this->db->bloco()->where('plano_id',$id) as $bloco){
            $this->db->blocoexercicio()->where('bloco_id',$bloco['id'])->delete();
            $bloco->delete();
        }


        $result = $this->db->plano()->where('id', $id)->delete();

        return $result;
    }

    public function associate($data)
    {
        $userDAO = new UserDAO();

        $data['atleta_id'] = $userDAO->getAtletaByUserID($data['atleta_id']);

        $result = $this->db->planoatleta()->insert($data);
        return $result;
    }

    public function dissociate($data)
    {
        $userDAO = new UserDAO();

        $data['atleta_id'] = $userDAO->getAtletaByUserID($data['atleta_id']);

        $result = $this->db->planoatleta()->where('plano_id', $data['plano_id'])->where('atleta_id', $data['atleta_id'])->delete();
        return $result;
    }

    public function addExercise($data)
    {
        $result = $this->db->blocoexercicio()->insert($data);
        return $result;
    }

    public function getBloco($data)
    {
        foreach ($this->db->bloco()->where('plano_id', $data['plano_id'])->and('dia', $data['dia']) as $bloco) {
            return $bloco['id'];
        }

        return false;
    }

    public function showBloco($id)
    {
        $exercicioDAO = new ExercicioDAO();

        $data = array();

        foreach ($this->db->blocoexercicio()->where('bloco_id', $id) as $exercicio) {
            array_push($data, [
                'bloco_id' => $exercicio['bloco_id'],
                'exercicio_id' => $exercicio['exercicio_id'],
                'exercicio' => $exercicioDAO->get($exercicio['exercicio_id']),
                'series' => $exercicio['series'],
                'repeticoes' => $exercicio['repeticoes'],
                'carga' => $exercicio['carga'],
                'tempo_distancia' => $exercicio['tempo_distancia'],
                'realizado' => $exercicio['realizado'] == 1 ? true : false
            ]);
        }

        return $data;
    }

    public function getPlanosAtleta($id)
    {
        $userDAO = new UserDAO();

        if (!$userDAO->isAtleta($id)) return null;

        $data = array();

        $userID = $this->db->atleta("utilizador_id = ?", $id)->fetch()['id'];

        foreach ($this->db->planoatleta()->where('atleta_id', $userID) as $row) {
            foreach ($this->db->plano()->where('id', $row['plano_id']) as $plano) {

                $from = date_create($plano['data_inicial']);
                $to = date_create($plano['data_final']);
                $days = date_diff($to, $from)->days;

                $treinador = $userDAO->getUser($userDAO->getTreinador($plano['treinador_id'])['utilizador_id']);

                array_push($data, [
                    'id' => $plano['id'],
                    'treinador' => $treinador,
                    'nome' => $plano['nome'],
                    'descricao' => $plano['descricao'],
                    'data_inicial' => $plano['data_inicial'],
                    'data_final' => $plano['data_final'],
                    'dias' => $days,
                    'atletas_associados' => $this->getAtletasAssociados($plano['id']),
                    'blocos' => $this->getBlocos($plano['id'])
                ]);
            }
        }


        return $data;
    }

    public function getPlanosTreinador($id)
    {
        $userDAO = new UserDAO();

        if (!$userDAO->isTreinador($id)) return null;

        $data = array();

        $treinador_id = $userDAO->getTreinadorByUserID($id)['id'];

        foreach ($this->db->plano()->where('treinador_id', $treinador_id) as $plano) {

            $from = date_create($plano['data_inicial']);
            $to = date_create($plano['data_final']);
            $days = date_diff($to, $from)->days;

            $treinador = $userDAO->getUser($treinador_id);

            array_push($data, [
                'id' => $plano['id'],
                'treinador' => $treinador,
                'nome' => $plano['nome'],
                'descricao' => $plano['descricao'],
                'data_inicial' => $plano['data_inicial'],
                'data_final' => $plano['data_final'],
                'dias' => $days,
                'atletas_associados' => $this->getAtletasAssociados($plano['id']),
                'blocos' => $this->getBlocos($plano['id'])
            ]);
        }


        return $data;
    }

    public function getAtletasAssociados($id)
    {
        $userDAO = new UserDAO();

        $data = array();

        foreach ($this->db->planoatleta()->where('plano_id', $id) as $plano) {
            $userID = $userDAO->getAtleta($plano['atleta_id'])['utilizador_id'];

            array_push($data, $userDAO->getUser($userID));
        }

        return $data;
    }

    public function updateExercise($bloco, $exercicio, $data)
    {
        return $this->db->blocoexercicio()
            ->where("bloco_id", $bloco)
            ->where("exercicio_id", $exercicio)
            ->update($data);
    }

    public function deleteExercise($bloco, $exercicio)
    {
        return $this->db->blocoexercicio()
            ->where("bloco_id", $bloco)
            ->where("exercicio_id", $exercicio)
            ->delete();
    }

    public function duplicate($data)
    {
        $old_plano = $this->getPlano($data['id']);

        $from = date_create($data['data_inicial']);
        $to = date_create($data['data_final']);
        $days = date_diff($to, $from)->days;


        $new_plano = $this->insert([
            'treinador_id' => $this->db->treinador('id = ?', $data['treinador_id'])->fetch()['utilizador_id'],
            'nome' => $data['nome'],
            'descricao' => $data['descricao'],
            'data_inicial' => $data['data_inicial'],
            'data_final' => $data['data_final']
        ]);

        for ($i = 0; $i < $days; $i++) {

            $bloco = $this->getBloco([
                'plano_id' => $new_plano['id'],
                'dia' => $i
            ]);

            foreach ($old_plano['blocos'][$i] as $exercicio) {
                $this->db->blocoexercicio()->insert([
                    'bloco_id' => $bloco,
                    'exercicio_id' => $exercicio['exercicio_id'],
                    'series' => $exercicio['series'],
                    'repeticoes' => $exercicio['repeticoes'],
                    'carga' => $exercicio['carga'],
                    'tempo_distancia' => $exercicio['tempo_distancia'],
                    'realizado' => false
                ]);
            }
        }

        return $this->getPlano($new_plano['id']);
    }
}
