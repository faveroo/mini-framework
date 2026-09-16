<?php

use W0q\Request\Controllers\HomeController;
use W0q\Request\Core\Container;
use W0q\Request\Http\Request;
use W0q\Request\Http\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$request = Request::capture();
$container = new Container();

$container->bind(
    Request::class,
    fn () => $request
    );
    
$router = new Router($container);

$router->get('/{id}', [HomeController::class, 'index']);

$response = $router->dispatch($request);

header('Content-Type: application/json');

echo json_encode(
    $response,
    JSON_PRETTY_PRINT
);