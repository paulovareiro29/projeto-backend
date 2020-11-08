<?php
//require_once('routes/user.php');

use App\Controllers\UserController;
use App\Middlewares\AuthMiddleware;

use function src\slimConfiguration;

$app = new \Slim\App(slimConfiguration());

// --------------------------------------

$app->group('/user', function() use ($app){
    $app->get('/', UserController::class . ':index');
    $app->get('/{id}', UserController::class . ':show');
    $app->put('/{id}', UserController::class . ':update');

    $app->group('', function() use ($app){
        $app->post('/', UserController::class . ':create');
    
        $app->delete('/{id}', UserController::class . ':delete');
    
        $app->post('/associate', UserController::class . ':associate');
        $app->post('/dissociate', UserController::class . ':dissociate');
    })->add(AuthMiddleware::class . ':isAdmin');
    

});



$app->get('/', function ($request) {
    return "API projeto backend";
});

// --------------------------------------

$app->run();