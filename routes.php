<?php

$router->get('/therapists', function () use ($router) {
    if (!(new Auth())->isLoggedIn()) {
        $router->sendResponse(['message' => 'Unauthorized'], 401);
    }
    $router->sendResponse([(new TherapistController())->getAllTherapists($_GET)], 200);
});

$router->get('/therapist/([\d]+)', function ($id) use ($router) {
    if (!(new Auth())->isLoggedIn()) {
        $router->sendResponse(['message' => 'Unauthorized'], 401);
    }
    $router->sendResponse([(new TherapistController())->getTherapistById($id)], 200);
});

$router->get('/clients', function () use ($router) {
    if (!(new Auth())->isLoggedIn()) {
        $router->sendResponse(['message' => 'Unauthorized'], 401);
    }
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
    if (!(new Auth())->isLoggedIn()) {
        $router->sendResponse(['message' => 'Unauthorized'], 401);
    }
    $router->sendResponse([(new TherapistController())->getAllAppointments($_REQUEST)], 200);
});

$router->get('/stats', function () use ($router) {
    if (!(new Auth())->isLoggedIn()) {
        $router->sendResponse(['message' => 'Unauthorized'], 401);
    }
    $router->sendResponse([(new StatController())->getStats()], 200);
});

$router->post('/login', function () use ($router) {
    $body = json_decode(file_get_contents('php://input'), true);
    $router->sendResponse([(new UserController())->login($body['username'], $body['password'])], 200);
});

$router->get('/auth', function () use ($router) {
    if (!(new Auth())->isLoggedIn()) {
        $router->sendResponse(['message' => 'Authentication Failed'], 403);
        return false;
    }
    $router->sendResponse(['message' => true], 200);
});

$router->post('/logout', function () use ($router) {
    $router->sendResponse([(new Auth())->logout()], 200);
});
