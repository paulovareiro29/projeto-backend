<?php
//require_once('routes/user.php');

use App\Controllers\ExercicioController;
use App\Controllers\PlanoController;
use App\Controllers\UserController;
use App\Middlewares\AuthMiddleware;

use function src\slimConfiguration;

$app = new \Slim\App(slimConfiguration());

// --------------------------------------
$app->post('/login', UserController::class . ':login');


$app->group('/user', function() use ($app){
    $app->get('/', UserController::class . ':index');
    $app->get('/{id}', UserController::class . ':show');
    $app->put('/{id}', UserController::class . ':update');
    $app->get('/token/{token}', UserController::class . ':getByToken');
    $app->put('/token/{token}', UserController::class . ':updateByToken');
    $app->group('', function() use ($app){
        $app->post('/', UserController::class . ':create');
    
        $app->delete('/{id}', UserController::class . ':delete');
    
        $app->post('/associate', UserController::class . ':associate');
        $app->post('/dissociate', UserController::class . ':dissociate');
    })->add(AuthMiddleware::class . ':isAdmin'); //somente admins podem fazer isto
});

//plano
$app->group('/plano', function() use($app){

    $app->get('/{id}/{dia}', PlanoController::class . ':showBloco');

    //atletas/admins
    $app->group('', function () use ($app){
        $app->get('/atleta/{id}', PlanoController::class . ':planosAtleta');

    })->add(AuthMiddleware::class . ':isAtleta');


    //treinadores/admins
    $app->group('', function() use ($app){
        $app->get('/{id}', PlanoController::class . ':show');
        $app->get('/treinador/{id}', PlanoController::class . ':planosTreinador');
        $app->post('/', PlanoController::class . ':create');
        $app->post('/associate', PlanoController::class . ':associate');
        $app->post('/{id}/add', PlanoController::class . ':addExercise');

        $app->put('/{id}', PlanoController::class . ':update');
        $app->delete('/{id}', PlanoController::class . ':delete');
    })->add(AuthMiddleware::class . ':isTreinador');


    //admins
    $app->group('',function() use($app){
        $app->get('/', PlanoController::class . ':index');

    })->add(AuthMiddleware::class . ':isAdmin');
});

//exercicio
$app->group('/exercicio', function() use ($app){

    //treinador
    $app->group('', function() use ($app){
        $app->get('/', ExercicioController::class . ':index');
        $app->get('/{id}', ExercicioController::class . ':show');
        $app->post('/', ExercicioController::class . ':create');
        $app->put('/{id}', ExercicioController::class . ':update');
        $app->delete('/{id}',  ExercicioController::class . ':delete');


        $app->get('/tipoexercicio/', ExercicioController::class . ':indexTipoExercicio');
        $app->get('/tipoexercicio/{id}', ExercicioController::class . ':showTipoExercicio');
        $app->post('/tipoexercicio', ExercicioController::class . ':createTipoExercicio');
        $app->put('/tipoexercicio/{id}', ExercicioController::class . ':updateTipoExercicio');
        $app->delete('/tipoexercicio/{id}',  ExercicioController::class . ':deleteTipoExercicio');
        
    })->add(AuthMiddleware::class . ':isTreinador');
});



$app->get('/', function ($request) {
    return "API projeto backend";
});

// --------------------------------------

$app->run();