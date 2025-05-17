<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../Helpers/functions.php';

use App\Classes\Router;

$url = $_SERVER['REQUEST_URI'];
$router = new Router($url);

$router->addRoute('sobre', 'About', 'index');
$router->addRoute('contato/enviar', 'Contact', 'send');
$router->addRoute('produtos/{id}', 'Product', 'show');

$router->dispatch();
?>