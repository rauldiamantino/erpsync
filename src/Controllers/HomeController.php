<?php

namespace App\Controllers;

use App\Controllers\Controller;

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
    $this->view->assign('title', 'Home');
    $this->view->render('index');
  }
}
