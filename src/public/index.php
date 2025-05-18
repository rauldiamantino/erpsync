<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../Helpers/functions.php';

use App\Classes\Router;

$url = $_SERVER['REQUEST_URI'];
$router = new Router($url);

$router->addRoute('/', 'Home', 'index');

$router->dispatch();