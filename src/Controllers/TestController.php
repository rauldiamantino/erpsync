<?php

namespace App\Controllers;

use App\Classes\View;

class TestController
{
  public function index(): void
  {
    $view = new View();

    $view->assign('title', "Page's Tests");

    $view->setLayout('default');
    $view->setFolder('Test');
    $view->render('index');
  }
}
