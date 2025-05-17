<?php

namespace App\Controllers;

use App\Classes\View;

class HomeController
{
  public function index(): void
  {
    $view = new View();

    $blingBaseUrl = 'https://www.bling.com.br/Api/v3/oauth/authorize';
    $blingClientId = getSetting('bling_client_id');
    $blingState = 'e2eb8eba1bb80743c26607bf80aa1317';

    $blingAuthUrl = $blingBaseUrl . '?response_type=code&client_id=' . $blingClientId . '&state=' . $blingState;

    $view->assign('title', 'Página Inicial');
    $view->assign('blingAuthUrl', $blingAuthUrl);

    $view->setLayout('default');
    $view->setFolder('Home');
    $view->render('index');
  }
}
