<?php

namespace App\Controllers;

use App\Classes\View;

Class Controller
{
  protected $view;
  protected $folder;
  protected $layout;

  public function __construct()
  {
    $this->view = new View();

    $this->view->setLayout($this->layout);
    $this->view->setFolder($this->folder);
  }
}