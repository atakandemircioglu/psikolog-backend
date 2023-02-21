<?php

if (isset($_GET['dbg'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

require_once('src/Bootstrap.php');

$app = new Bootstrap();
$router = $app->router;

require_once('routes.php');
