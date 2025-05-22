<?php
session_start();

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../Helpers/functions.php';

use App\Classes\Router;

$router = new Router();

require_once __DIR__ . '/../Routes/web.php';

$router->dispatch();