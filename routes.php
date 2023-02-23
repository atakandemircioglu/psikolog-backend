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
    if (!(new Auth())->isLoggedIn()) {
        $router->sendResponse(['message' => 'Unauthorized'], 401);
    }
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
    if (!(new Auth())->isLoggedIn()) {
        $router->sendResponse(['message' => 'Unauthorized'], 401);
    }
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
    $router->sendResponse([(new TherapistController())->getAllAppointments()], 200);
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

$router->options('/login', function () use ($router) {
    header('Access-Control-Allow-Origin: http://localhost:3000');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    $router->sendResponse([], 200);
});
