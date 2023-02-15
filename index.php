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

$router->get('/set-calendar', function () use ($router) {
    $obj = new TherapistController();
    $router->sendResponse([$obj->createAppointmentForm()], 200);
});

$router->get('/therapist-register', function () use ($router) {
    $body = json_decode(file_get_contents('php://input'), true);
    $obj = new TherapistController();
    $router->sendResponse([$obj->onTherapistRegister($body)], 200);
});
