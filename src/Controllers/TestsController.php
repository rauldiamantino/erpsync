<?php

namespace App\Controllers;

use App\Controllers\Controller;

class TestsController extends Controller
{
  protected $folder = 'tests';
  protected $layout = 'tests';

  public function __construct()
  {
    parent::__construct();
  }

  public function index(): void
  {
    $this->view->assign('result', $result ?? []);

    $this->view->assign('title', 'Page\'s Tests');
    $this->view->render('index');
  }
}
