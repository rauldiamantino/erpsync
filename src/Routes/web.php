<?php

$router->addRoute('/integration_tasks/receive_category/{id}', 'IntegrationTasks', 'receiveCategory');
$router->addRoute('/integration_tasks/receive_supplier/{id}', 'IntegrationTasks', 'receiveSupplier');
$router->addRoute('/integration_tasks/receive_product/{id}', 'IntegrationTasks', 'receiveProduct');
$router->addRoute('/integration_tasks/send_category/{id}', 'IntegrationTasks', 'sendCategory');
$router->addRoute('/integration_tasks/send_supplier/{id}', 'IntegrationTasks', 'sendSupplier');
$router->addRoute('/integration_tasks/send_product/{id}', 'IntegrationTasks', 'sendProduct');
