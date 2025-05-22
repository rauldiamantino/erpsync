<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../Helpers/functions.php';

use App\Classes\Router;

$url = $_SERVER['REQUEST_URI'];
$router = new Router($url);

$router->addRoute('/integration_tasks/receive_category/{id}', 'IntegrationTasks', 'receiveCategory');
$router->addRoute('/integration_tasks/receive_product/{id}', 'IntegrationTasks', 'receiveProduct');
$router->addRoute('/integration_tasks/send_category/{id}', 'IntegrationTasks', 'sendCategory');
$router->addRoute('/integration_tasks/send_product/{id}', 'IntegrationTasks', 'sendProduct');

$router->dispatch();

if ($url == '/') {
  header('location: /integration_tasks');
  exit;
}