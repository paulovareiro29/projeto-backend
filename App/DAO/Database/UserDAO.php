<?php

namespace App\DAO\Database;

class UserDAO extends Connection
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllUsers(): array
    {
        $data = array();

        foreach ($this->db->utilizador() as $user) {
            $admin = $this->isAdmin($user['id']);
            $atleta = $this->isAtleta($user['id']);
            $treinador = $this->isTreinador($user['id']);

            array_push($data, [
                'id' => $user['id'],
                'atleta_id' => $this->db->atleta('utilizador_id = ?', $user['id'])->fetch()['id'],
                'treinador_id' => $this->db->treinador('utilizador_id = ?', $user['id'])->fetch()['id'],
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

    public function getUser($id): array
    {
        foreach ($this->db->utilizador()
            ->where('id', $id) as $user) {
            $admin = $this->isAdmin($user['id']);
            $atleta = $this->isAtleta($user['id']);
            $treinador = $this->isTreinador($user['id']);

            return [
                'id' => $user['id'],
                'atleta_id' => $this->db->atleta('utilizador_id = ?', $user['id'])->fetch()['id'],
                'treinador_id' => $this->db->treinador('utilizador_id = ?', $user['id'])->fetch()['id'],
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

    public function login($body)
    {

        foreach ($this->db->utilizador()->where('username', $body['username']) as $user) {
            if (password_verify($body['pass'], $user['pass'])) {
                $token = bin2hex(openssl_random_pseudo_bytes(16));

                $user->update(['access_token' => $token]);
                return $token;
            }
        }

        return null;
    }

    public function getUserByToken($token)
    {
        $user = $this->db->utilizador('access_token = ?', $token)->fetch();
        if(!$user){
            return null;
        }

        $admin = $this->isAdmin($user['id']);
        $atleta = $this->isAtleta($user['id']);
        $treinador = $this->isTreinador($user['id']);

        return [
            'id' => $user['id'],
            'atleta_id' => $this->db->atleta('utilizador_id = ?', $user['id'])->fetch()['id'],
            'treinador_id' => $this->db->treinador('utilizador_id = ?', $user['id'])->fetch()['id'],
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

    public function insert($user, $roles): bool
    {

        if(!isset($user['pass']) || !isset($user['username'])) return false;

        if($this->db->utilizador()->where('username',$user['username'])->fetch()) return false;

        

        if(isset($user['pass'])){
            $user['pass'] = password_hash($user['pass'],PASSWORD_BCRYPT);
        }

        $id = $this->db->utilizador()->insert($user);

        if (!$id) return false;

        $this->db->administrador()->insert(['utilizador_id' => $id, 'active' => $roles['admin']]);
        $this->db->atleta()->insert(['utilizador_id' => $id, 'active' => $roles['atleta']]);
        $this->db->treinador()->insert(['utilizador_id' => $id, 'active' => $roles['treinador']]);

        return true;
    }

    public function update($user, $roles)
    {
        if(isset($user['pass'])){
            $user['pass'] = password_hash($user['pass'],PASSWORD_BCRYPT);
        }

        $utilizador = $this->db->utilizador[$user['id']];
        $admin =  $this->db->administrador()->where('utilizador_id', $user['id']);
        $atleta = $this->db->atleta()->where('utilizador_id', $user['id']);
        $treinador =  $this->db->treinador()->where('utilizador_id', $user['id']);

        if (!$utilizador) return false;

        $utilizador->update($user);
        if(isset($roles['admin'])) $admin->update(['active' => $roles['admin']]);
        if(isset($roles['atleta']))  $atleta->update(['active' => $roles['atleta']]);
        if(isset($roles['treinador']))  $treinador->update(['active' => $roles['treinador']]);

        return true;
    }

    public function delete($id): bool
    {
        $user = $this->db->utilizador[$id];

        $this->db->administrador()->where('utilizador_id', $id)->delete();
        $this->db->atleta()->where('utilizador_id', $id)->delete();
        $this->db->treinador()->where('utilizador_id', $id)->delete();

        $result = $user->delete();

        return $result;
    }

    public function associate($data)
    {
        if (!$this->isAtleta($data['atleta_id']) || !$this->isTreinador($data['treinador_id']))
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

    public function dissociate($data)
    {
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

    public function getAssociatedTreinadores(int $id)
    {
        if (!$this->isAtleta($id)) return null;

        $data = array();

        $atleta_id = $this->getAtletaByUserID($id)['id'];

        foreach ($this->db->atletatreinador()
            ->where('atleta_id', $atleta_id) as $row) {

            $treinador_id = $this->getTreinador($row['treinador_id'])['utilizador_id'];

            foreach ($this->db->utilizador()->where('id', $treinador_id) as $user) {
                array_push($data, [
                    'id' => $user['id'],
                    'nome' => $user['nome']
                ]);
            }
        }


        return $data;
    }

    public function getAssociatedAtletas(int $id)
    {
        if (!$this->isTreinador($id)) return null;

        $data = array();

        $treinador_id = $this->getTreinadorByUserID($id)['id'];

        foreach ($this->db->atletatreinador()
            ->where('treinador_id', $treinador_id) as $row) {

            $atleta_id = $this->getAtleta($row['atleta_id'])['utilizador_id'];

            foreach ($this->db->utilizador()->where('id', $atleta_id) as $user) { //vai dar query ao utilizador
                array_push($data, [ //e puxar para o array data a informaçao
                    'id' => $user['id'],
                    'nome' => $user['nome']
                ]);
            }
        }
        return $data;
    }

    public function isAdmin(int $id): bool
    {
        foreach ($this->db->administrador()->where('utilizador_id', $id) as $admin)
            if ($admin['active'])
                return true;

        return false;
    }

    public function isAtleta(int $id): bool
    {
        foreach ($this->db->atleta()->where('utilizador_id', $id) as $atleta)
            if ($atleta['active'])
                return true;

        return false;
    }

    public function isTreinador(int $id): bool
    {
        foreach ($this->db->treinador()->where('utilizador_id', $id) as $treinador)
            if ($treinador['active'])
                return true;

        return false;
    }

    public function getAdmin(int $id)
    {
        foreach ($this->db->administrador()
            ->where('id', $id) as $admin) {
            return $admin;
        }
    }

    public function getAtleta(int $id)
    {
        foreach ($this->db->atleta()
            ->where('id', $id) as $atleta) {
            return $atleta;
        }
    }

    public function getTreinador(int $id)
    {
        foreach ($this->db->treinador()
            ->where('id', $id) as $treinador) {
            return $treinador;
        }
    }

    public function getAdminByUserID(int $id)
    { //devolve atleta apartir do id de utilizador
        foreach ($this->db->administrador()
            ->where('utilizador_id', $id) as $admin) {
            return $admin;
        }
    }

    public function getAtletaByUserID(int $id)
    { //devolve atleta apartir do id de utilizador
        foreach ($this->db->atleta()
            ->where('utilizador_id', $id) as $atleta) {
            return $atleta;
        }
    }

    public function getTreinadorByUserID(int $id)
    { //devolve atleta apartir do id de utilizador
        foreach ($this->db->treinador()
            ->where('utilizador_id', $id) as $treinador) {
            return $treinador;
        }
    }
}
