<?php

$app->get('/user', 'User::index');
$app->get('/user/{id}','User::show');
$app->post('/user','User::create');
$app->delete('/user/{id}','User::delete');

$app->get('/user/associate/{id_treinador}/{id_atleta}', 'User::associate');



class User {
    /*function isAdmin($id){
        require_once(__DIR__ . '\..\database\dbconnect.php');
    
        foreach($db->administrador()->where('utilizador_id',$id) as $ad)
                if($ad) return true;
    
        return false;
    }*/

    function index($request){ //por a mostrar se é atleta admin treinador
        require_once(__DIR__ . '\..\database\dbconnect.php');

        $data = array();

        foreach ($db->utilizador() as $user){
            $admin = false;
            $atleta = false;
            $treinador = false;

            //roles
            foreach($db->administrador()->where('utilizador_id',$user['id']) as $ad)
                if($ad) $admin = true;
            
            foreach($db->atleta()->where('utilizador_id',$user['id']) as $at)
                if($at) $atleta = true;

            foreach($db->treinador()->where('utilizador_id',$user['id']) as $tr)
                if($tr) $treinador = true;

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
                   ]
               ]);
        }

        if(isset($data))
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        
        return json_encode("ERROR 404: USER", JSON_UNESCAPED_UNICODE);
    }

    function show($request, $response){ //por a mostrar se é atleta admin treinador
        require_once(__DIR__ . '\..\database\dbconnect.php');
        $id = $request->getAttribute('id');

        foreach ($db->utilizador()
                    ->where('id', $id) as $row){
            $data[] = $row;
        };

        if(isset($data))
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        
        return json_encode("ERROR 404: USER", JSON_UNESCAPED_UNICODE);

    }

    function create($request, $response){
        require_once(__DIR__ . '\..\database\dbconnect.php');
        $body = $request->getParsedBody(); 

        $user_fields = [
            'username',
            'pass',
            'nome',
            'email',
            'morada',
        ];
        
        $user_roles = [
            'admin',
            'atleta',
            'treinador'
        ];

        $user_body = array();
        $roles_body = array();

        foreach($user_roles as $role){ //loop para se a role for vazia atribuir false
            if(!isset($body[$role])) $body[$role] = 'false';
            $roles_body[$role] = ($body[$role] == 'true'? true : false);
        }

        foreach($user_fields as $field){ //loop para se algum campo do user for vazio atribuir null
            if(!isset($body[$field])){
                $user_body[$field] = null;
                continue;
            }
            $user_body[$field] = $body[$field];
        }
        
        if(isset($user_body['pass']))
            $user_body['pass'] = hash('md5', $user_body['pass']);

        $user = $db->utilizador();
        $result = $user->insert($user_body);

        if($roles_body['admin']){$db->administrador()->insert(['utilizador_id' => $result]);}
        if($roles_body['atleta']){$db->atleta()->insert(['utilizador_id' => $result]);}
        if($roles_body['treinador']){$db->treinador()->insert(['utilizador_id' => $result]);}

        $result = ($result == false ? 
            $result = ['status' => false, 'message' => 'Erro ao inserir registo'] :
            $result = ['status' => true, 'message' => 'Registo inserido com sucesso. id: ' . $result['id']]);

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    function update($request){
        require_once(__DIR__ . '\..\database\dbconnect.php');
        $body = $request->getParsedBody();

    }

    function delete($request){
        require_once(__DIR__ . '\..\database\dbconnect.php');
        $id = $request->getAttribute('id');

        $user = $db->utilizador[$id];
        
        if(!$user) return json_encode(
            ['status' => false,
             'message' => 'Registo não existe']
        , JSON_UNESCAPED_UNICODE);

        $result = $user->delete();

        $result = ($result ? 
            $result = ['status' => true, 'message' => 'Registo apagado com sucesso'] :
            $result = ['status' => false, 'message' => 'Erro ao apagar registo']
        );

        return json_encode($result, JSON_UNESCAPED_UNICODE);

    }

    function associate($request){
        require_once(__DIR__ . '\..\database\dbconnect.php');

        $treinador = $request->getAttribute('id_treinador');
        $atleta = $request->getAttribute('id_atleta');

        $body = [
            'atleta_id' => $atleta,
            'treinador_id' => $treinador
        ];

        $result = $db->atletatreinador()->insert($body);

        $result = ($result == false ? 
            $result = ['status' => false, 'message' => 'Erro ao associar'] :
            $result = ['status' => true, 'message' => 'Registos associados com sucesso.']);

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }


}