<?php

$router->get('/', function () use ($router) {
    $router->sendResponse(["message" => "homepage"], 200);
});

$router->get('/therapists', function () use ($router) {
    $obj = new TherapistController();
    $router->sendResponse([$obj->getAllTherapists($_GET)], 200);
});

$router->get('/clients', function () use ($router) {
    $obj = new ClientController();
    $router->sendResponse([$obj->getAllClients($_GET)], 200);
});

$router->get('/client-options', function () use ($router) {
    $obj = new ClientController();
    $result = $obj->getAllClients($_GET);
    $response = [];
    foreach ($result as $eachResponse) {
        $response[] = [
            'label' => sprintf('%s %s', $eachResponse['isim']['first'], $eachResponse['isim']['last']),
            'value' => $eachResponse['eposta']
        ];
    }
    $router->sendResponse($response, 200);
});

$router->post('/therapist-register', function () use ($router) {
    try {
        $obj = new TherapistController();
        $router->sendResponse([$obj->onTherapistRegister($_REQUEST)], 200);
    } catch (Exception $e) {
        $router->sendResponse([$e->getMessage()], 200);
    }
});

$router->get('/therapist-appointments', function () use ($router) {
    $obj = new TherapistController();
    $router->sendResponse([$obj->getAllAppointments()], 200);
});

$router->get('/stats', function () use ($router) {
    $obj = new StatController();
    $router->sendResponse([$obj->getStats()], 200);
});
