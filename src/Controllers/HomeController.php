<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Helpers\RedirectHelper;

class HomeController extends Controller
{
  protected $layout = 'default';
  protected $folder = 'home';

  public function __construct()
  {
    parent::__construct();
  }

  public function index(): void
  {
    RedirectHelper::to('/integration_tasks');

    $this->view->assign('title', 'Home');
    $this->view->render('index');
  }
}
