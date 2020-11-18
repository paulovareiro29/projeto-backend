<?php

namespace App\DAO\Database;

class UserDAO extends Connection {

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllUsers(): array{
        $data = array();

        foreach ($this->db->utilizador() as $user){
            $admin = $this->isAdmin($user['id']);
            $atleta = $this->isAtleta($user['id']);
            $treinador = $this->isTreinador($user['id']);

               array_push($data,[
                   'id' => $user['id'],
                   'username' => $user['username'],
                   'pass' => $user['pass'],
                   'nome' => $user['nome'],
                   'email' => $user['email'],
                   'morada' => $user['morada'],
                   'roles' => [
                       'admin' => $admin,
                       'atleta' => $atleta,
                       'treinador' => $treinador
                   ],
                   'treinadores' => $this->getAssociatedTreinadores($user['id']),
                   'atletas' => $this->getAssociatedAtletas($user['id'])
               ]);
        }


        return $data;
    }

    public function getUser($id): array{
        foreach ($this->db->utilizador()
                                ->where('id', $id) as $user){
            $admin = $this->isAdmin($user['id']);
            $atleta = $this->isAtleta($user['id']);
            $treinador = $this->isTreinador($user['id']);

            return [
                'id' => $user['id'],
                'username' => $user['username'],
                'pass' => $user['pass'],
                'nome' => $user['nome'],
                'email' => $user['email'],
                'morada' => $user['morada'],
                'roles' => [
                    'admin' => $admin,
                    'atleta' => $atleta,
                    'treinador' => $treinador
                ],
                'treinadores' => $this->getAssociatedTreinadores($user['id']),
                'atletas' => $this->getAssociatedAtletas($user['id'])
            ];
        }
    }

    public function login($body){
        foreach($this->db->utilizador()->where('username', $body['username']) as $user){
            if($user['pass'] == $body['password']){
                return  bin2hex(openssl_random_pseudo_bytes(16));
            }
        }

        return null;
    }

    public function insert($user, $roles): bool{
        $id = $this->db->utilizador()->insert($user);

        if(!$id) return false;

        $this->db->administrador()->insert(['utilizador_id' => $id , 'active' => $roles['admin']]);
        $this->db->atleta()->insert(['utilizador_id' => $id , 'active' => $roles['atleta']]);
        $this->db->treinador()->insert(['utilizador_id' => $id , 'active' => $roles['treinador']]);

        return true;
    }

    public function update($user, $roles): bool{
        $utilizador = $this->db->utilizador()->where('id', $user['id']);
        $admin =  $this->db->administrador()->where('utilizador_id', $user['id']);
        $atleta = $this->db->atleta()->where('utilizador_id', $user['id']);
        $treinador =  $this->db->treinador()->where('utilizador_id', $user['id']);

        if(!$utilizador) return false;

        $utilizador->update($user);
        $admin->update(['active' => $roles['admin']]);
        $atleta->update(['active' => $roles['atleta']]);
        $treinador->update(['active' => $roles['treinador']]);

        return true;
    }

    public function delete($id): bool{
        $user = $this->db->utilizador[$id];

        $this->db->administrador()->where('utilizador_id', $id)->delete();
        $this->db->atleta()->where('utilizador_id', $id)->delete();
        $this->db->treinador()->where('utilizador_id', $id)->delete();

        $result = $user->delete();

        return $result;
    }

    public function associate($data){
        if(!$this->isAtleta($data['atleta_id']) || !$this->isTreinador($data['treinador_id'])) 
                return false;

        $atleta = $this->getAtletaByUserID($data['atleta_id']);
        $treinador = $this->getTreinadorByUserID($data['treinador_id']);

        $data = [
            'atleta_id' => $atleta['id'],
            'treinador_id' => $treinador['id']
        ];

        $result = $this->db->atletatreinador()->insert($data);
        return $result;
    }

    public function dissociate($data){
        $atleta = $this->getAtletaByUserID($data['atleta_id']);
        $treinador = $this->getTreinadorByUserID($data['treinador_id']);

        $data = [
            'atleta_id' => $atleta['id'],
            'treinador_id' => $treinador['id']
        ];

        $result = $this->db->atletatreinador()
                    ->where('treinador_id', $data['treinador_id'])
                    ->and('atleta_id', $data['atleta_id'])->delete();

        return $result;
    }

    public function getAssociatedTreinadores(int $id){
        if(!$this->isAtleta($id)) return null;
        
        $data = array();

            $atleta_id = $this->getAtletaByUserID($id)['id'];

            foreach($this->db->atletatreinador()
            ->where('atleta_id', $atleta_id) as $row) {

                $treinador_id = $this->getTreinador($row['treinador_id'])['utilizador_id'];

                foreach ($this->db->utilizador()->where('id', $treinador_id) as $user){
                    array_push($data,[
                        'id' => $user['id'],
                        'nome' => $user['nome']
                    ]);
                } 
            }


        return $data;
    }

    public function getAssociatedAtletas(int $id){
        if(!$this->isTreinador($id)) return null;
        
        $data = array();

        $treinador_id = $this->getTreinadorByUserID($id)['id'];

        foreach($this->db->atletatreinador()
        ->where('treinador_id',$treinador_id) as $row){

            $atleta_id = $this->getAtleta($row['atleta_id'])['utilizador_id'];
            
                foreach ($this->db->utilizador()->where('id', $atleta_id) as $user){ //vai dar query ao utilizador
                    array_push($data,[ //e puxar para o array data a informaçao
                        'id' => $user['id'],
                        'nome' => $user['nome']
                    ]);
                } 

        }
        return $data;
    }

    public function isAdmin(int $id): bool {
        foreach($this->db->administrador()->where('utilizador_id',$id) as $admin)
            if($admin['active'])
                return true;

        return false;
    }

    public function isAtleta(int $id): bool {
        foreach($this->db->atleta()->where('utilizador_id',$id) as $atleta)
                if($atleta['active'])
                    return true;

        return false;
    }

    public function isTreinador(int $id): bool {
        foreach($this->db->treinador()->where('utilizador_id',$id) as $treinador)
            if($treinador['active'])
                return true;

        return false;
    }

    public function getAdmin(int $id){ 
        foreach ($this->db->administrador()
                    ->where('id',$id) as $admin){
            return $admin;
        }
    }

    public function getAtleta(int $id) { 
        foreach ($this->db->atleta()
                    ->where('id',$id) as $atleta){
            return $atleta;
        }
    }

    public function getTreinador(int $id){ 
        foreach ($this->db->treinador()
                    ->where('id',$id) as $treinador){
            return $treinador;
        }
    }

    public function getAdminByUserID(int $id){ //devolve atleta apartir do id de utilizador
        foreach ($this->db->administrador()
                    ->where('utilizador_id',$id) as $admin){
            return $admin;
        }
    }

    public function getAtletaByUserID(int $id) { //devolve atleta apartir do id de utilizador
        foreach ($this->db->atleta()
                    ->where('utilizador_id',$id) as $atleta){
            return $atleta;
        }
    }

    public function getTreinadorByUserID(int $id){ //devolve atleta apartir do id de utilizador
        foreach ($this->db->treinador()
                    ->where('utilizador_id',$id) as $treinador){
            return $treinador;
        }
    }

    
}