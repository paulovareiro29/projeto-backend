<?php
$app->get('/user', 'User::index');
$app->post('/user','User::create');

class User {

    function index($request){
        require_once(__DIR__ . '\..\database\dbconnect.php');

        foreach ($db->utilizador() as $row){
            $data[] = $row;
        };

        if(isset($data))
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        
        return json_encode("ERROR 404: USER", JSON_UNESCAPED_UNICODE);
    }

    function create($request, $response){
        require_once(__DIR__ . '\..\database\dbconnect.php');
        $body = $request->getParsedBody(); 

        $nulls = [
            'username',
            'pass',
            'nome'
        ];

        foreach($nulls as $field){
            if(!isset($body[$field])) $body[$field] = null;
        }
        
        $user = $db->utilizador();
        $result = $user->insert($body);

        $result = ($result == false ? 
            $result = ['status' => false, 'message' => 'Erro ao inserir registo'] :
            $result = ['status' => true, 'message' => 'Registo inserido com sucesso. id: ' . $result['id']]);

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    function show($request, $response){

    }

}