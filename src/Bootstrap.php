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
        spl_autoload_register(function ($className) {
            # Usually I would just concatenate directly to $file variable below
            # this is just for easy viewing on Stack Overflow)
            $ds = DIRECTORY_SEPARATOR;
            $dir = __DIR__;

            // replace namespace separator with directory separator (prolly not required)
            $className = str_replace('\\', $ds, $className);

            // get full name of file containing the required class
            $file = "{$dir}{$ds}{$className}.php";

            // get file if it is readable
            if (is_readable($file)) {
                require_once $file;
            }
        });
    }

    public function initRouter()
    {
        $this->router = new Router(substr($_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['SCRIPT_NAME']))), $_SERVER['REQUEST_METHOD']);
    }

    public function initCORS()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: X-Requested-With');
        header('Content-type: application/json; charset=utf-8');
    }
}
