<?php

$router->get('/therapists', function () use ($router) {
    $router->sendResponse([(new TherapistController())->getAllTherapists($_GET)], 200);
}, [new AuthGuard()]);

$router->get('/clients', function () use ($router) {
    $router->sendResponse([(new ClientController())->getAllClients($_GET)], 200);
});

$router->get('/client-options', function () use ($router) {
    $clientController = new ClientController();
    $result = $clientController->getAllClients($_GET);
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
        $router->sendResponse([(new TherapistController())->onTherapistRegister($_REQUEST)], 200);
    } catch (Exception $e) {
        $router->sendResponse([$e->getMessage()], 200);
    }
});

$router->get('/therapist-appointments', function () use ($router) {
    $router->sendResponse([(new TherapistController())->getAllAppointments()], 200);
});

$router->get('/stats', function () use ($router) {
    $router->sendResponse([(new StatController())->getStats()], 200);
});

$router->post('/login', function () use ($router) {
    $body = json_decode(file_get_contents('php://input'), true);
    $router->sendResponse([(new UserController())->login($body['username'], $body['password'])], 200);
});
