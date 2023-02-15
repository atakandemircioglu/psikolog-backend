<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('src/Router.php');
include_once('src/Controllers/TherapistController.php');
include_once('src/TableDB/init.php');
include_once('src/TableDB/JotForm.php');

$router = new Router(substr($_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['SCRIPT_NAME']))), $_SERVER['REQUEST_METHOD']);

$router->get('/', function () use ($router) {
    $router->sendResponse(["message" => "homepage"], 200);
});

$router->get('/therapists', function () use ($router) {
    $obj = new TherapistController();
    $router->sendResponse([$obj->getAllTherapists()], 200);
});

$router->post('/therapist-register', function () use ($router) {
    file_put_contents(__DIR__ . '/log/test.json', json_encode($_REQUEST));
    $obj = new TherapistController();
    $router->sendResponse([$obj->onTherapistRegister($_REQUEST)], 200);
});
