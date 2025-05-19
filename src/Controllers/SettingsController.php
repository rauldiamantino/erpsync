<?php

namespace App\Controllers;

use App\Controllers\Controller;

class SettingsController extends Controller
{
  protected $layout = 'default';
  protected $folder = 'settings';

  public function __construct()
  {
    parent::__construct();
  }

  public function index(): void
  {
    // Bling Auth
    $blingBaseUrl = 'https://www.bling.com.br/Api/v3/oauth/authorize';
    $blingClientId = getSetting('bling_client_id');
    $blingState = getSetting('bling_state');
    $blingAuthUrl = $blingBaseUrl . '?response_type=code&client_id=' . $blingClientId . '&state=' . $blingState;

    $this->view->assign('blingAuthUrl', $blingAuthUrl);

    $this->view->assign('title', 'Página Inicial');

    $this->view->render('index');
  }
}