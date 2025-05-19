<?php

namespace App\Controllers;

use App\Classes\View;
use App\Helpers\Flash;

class Controller
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

  protected function redirect(string $url, string $type = null, string $message = null): void
  {
    if ($type) {
      Flash::set($type, $message);
    }

    header('Location: ' . $url);
    exit;
  }
}
