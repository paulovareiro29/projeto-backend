<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/vendor/autoload.php';

$app = new \Slim\App();

require_once('api/routes/user.php');

$app->get('/', function ($request) {
    return "API projeto backend";
});

$app->run();
?>


