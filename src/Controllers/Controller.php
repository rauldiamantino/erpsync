<?php

namespace App\Controllers;

use App\Classes\View;
use App\Helpers\Flash;
use App\Classes\Constants\ServiceType;
use App\Classes\Constants\ReferenceType;

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

  protected function getServiceName(?int $serviceType): string
  {
    $services = [
      ServiceType::BLING => 'Bling',
    ];

    return $services[ $serviceType ] ?? 'Desconhecido';
  }

  protected function getReferenceName(?int $referenceType): string
  {
    $referencesTypes = [
      ReferenceType::PRODUCT => 'Produto',
      ReferenceType::SKU => 'Sku',
      ReferenceType::CATEGORY => 'Categoria',
      ReferenceType::BRAND => 'Marca',
      ReferenceType::STOCK => 'Estoque',
    ];

    return $referencesTypes[ $referenceType ] ?? 'Desconhecido';
  }
}
