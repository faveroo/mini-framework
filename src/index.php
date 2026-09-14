<?php

use W0q\Request\Http\Request;
use W0q\Request\Http\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$request = Request::capture();

$router = new Router();

$router->get('/', function(Request $request) {
    return $request->all();
});

$response = $router->dispatch($request);

header('Content-Type: application/json');

echo json_encode(
    $response,
    JSON_PRETTY_PRINT
);