<?php

class Bootstrap
{
    public $router = null;

    public function __construct()
    {
        $this->loadClasses();
        $this->initCORS();
        $this->initRouter();
    }

    public function loadClasses()
    {
        // TODO :: add autoloader
        require_once('Controllers/StatController.php');
        require_once('Controllers/TherapistController.php');
        require_once('Controllers/ClientController.php');
        require_once('Controllers/UserController.php');
        require_once('Classes/Auth.php');
        require_once('TableDB/init.php');
        require_once('TableDB/JotForm.php');
        require_once('Router.php');
    }

    public function initCORS()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 1000');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
    }

    public function initRouter()
    {
        $this->router = new Router(substr($_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['SCRIPT_NAME']))), $_SERVER['REQUEST_METHOD']);
    }
}
