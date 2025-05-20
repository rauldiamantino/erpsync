<?php

namespace App\Controllers;

use App\Classes\Constants\ReferenceType;
use App\Classes\Constants\ServiceType;
use App\Controllers\Controller;
use App\Models\IntegrationTaskModel;
use App\Controllers\Components\BlingCategorySyncComponent;
use App\Controllers\Components\BlingProductSyncComponent;

class SyncController extends Controller
{
  protected $layout = false;
  protected $folder = false;

  private $integrationTaskModel;

  public function __construct()
  {
    parent::__construct();

    $this->integrationTaskModel = new IntegrationTaskModel();
  }

  public function sync(int $id = 0)
  {
    $resultTasks = $this->integrationTaskModel->all();

    if (empty($resultTasks)) {
      return ['error' => 'Registros não encontrados'];
    }

    $blingCategorySync = new BlingCategorySyncComponent();
    $blingProductSync = new BlingProductSyncComponent();

    foreach ($resultTasks as $value):

      if ($value['service'] !== ServiceType::BLING) {
        continue;
      }

      if ($value['type'] === ReferenceType::CATEGORY) {
        $response = $blingCategorySync->syncToEcommerce($value['reference_id']);
        die;
      }
      elseif ($value['type'] === ReferenceType::PRODUCT) {
        // $response = $blingProductSync->syncToEcommerce($value['reference_id']);
      }
    endforeach;
  }
}