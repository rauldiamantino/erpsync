<?php

namespace App\Controllers;

use App\Controllers\Controller;

class TestController extends Controller
{
  protected $folder = 'Test';
  protected $layout = 'test';

  public function __construct()
  {
    parent::__construct();
  }

  public function index(): void
  {
    // Always render before testing
    $this->view->assign('title', "Page's Tests");
    $this->view->render('index');

    $test = ['testing'];

    pr($test);
  }
}
