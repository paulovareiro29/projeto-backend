<?php
//require_once('routes/user.php');

use App\Controllers\ExercicioController;
use App\Controllers\PlanoController;
use App\Controllers\UserController;
use App\Middlewares\AuthMiddleware;

use function src\slimConfiguration;

$app = new \Slim\App(slimConfiguration());

$app->add(AuthMiddleware::class . ':headers');
//------------------------------------
    $app->post('/login', UserController::class . ':login');

    $app->put('/plano/blocoexercicio/{bloco}/{exercicio}', PlanoController::class . ':updateExercise');

    $app->group('/user', function () use ($app) {

        $app->get('/{id}', UserController::class . ':show');
        $app->put('/{id}', UserController::class . ':update');
        $app->get('/token/{token}', UserController::class . ':getByToken');
        $app->put('/token/{token}', UserController::class . ':updateByToken');
        $app->group('', function () use ($app) {
            $app->get('/', UserController::class . ':index');

            $app->post('/', UserController::class . ':create');

            $app->delete('/{id}', UserController::class . ':delete');

            $app->post('/associate', UserController::class . ':associate');
            $app->post('/dissociate', UserController::class . ':dissociate');
        })->add(AuthMiddleware::class . ':isAdmin'); //somente admins podem fazer isto
    });

    //plano
    $app->group('/plano', function () use ($app) {

        $app->get('/{id}/bloco/{dia}', PlanoController::class . ':showBloco'); //

        //atletas/admins
        $app->group('', function () use ($app) {
            $app->get('/atleta/{id}', PlanoController::class . ':planosAtleta'); //

        })->add(AuthMiddleware::class . ':isAtleta');


        //treinadores/admins
        $app->group('', function () use ($app) {
            $app->get('/{id}', PlanoController::class . ':show'); //
            $app->get('/treinador/{id}', PlanoController::class . ':planosTreinador'); //
            $app->get('/{id}/atletas', PlanoController::class . ':atletasAssociadosPlano'); //
            $app->post('/', PlanoController::class . ':create'); //
            $app->post('/associate', PlanoController::class . ':associate'); //
            $app->post('/dissociate', PlanoController::class . ':dissociate'); //
            $app->post('/{id}/add', PlanoController::class . ':addExercise'); //
            $app->post('/duplicate', PlanoController::class . ':duplicate');

            $app->put('/{id}', PlanoController::class . ':update'); //
            $app->delete('/{id}', PlanoController::class . ':delete'); //
            $app->delete('/blocoexercicio/{bloco}/{exercicio}', PlanoController::class . ':deleteExercise'); //
        })->add(AuthMiddleware::class . ':isTreinador');


        //admins
        $app->group('', function () use ($app) {
            $app->get('/', PlanoController::class . ':index'); //

        })->add(AuthMiddleware::class . ':isAdmin');
    });

    //exercicio
    $app->group('/exercicio', function () use ($app) {

        //treinador
        $app->group('', function () use ($app) {
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

    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
        $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
        return $handler($req, $res);
    });

$app->get('/', function ($request) {
    return "API projeto backend";
});

// --------------------------------------

$app->run();
