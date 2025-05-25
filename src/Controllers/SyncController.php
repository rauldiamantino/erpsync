<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\IntegrationLogModel;
use App\Models\IntegrationTaskModel;
use App\Classes\Constants\ServiceType;
use App\Classes\Constants\ReferenceType;
use App\Controllers\Components\BlingSkuSyncComponent;
use App\Controllers\Components\BlingProductSyncComponent;
use App\Controllers\Components\BlingCategorySyncComponent;
use App\Controllers\Components\BlingSupplierSyncComponent;
use App\Helpers\ConversionHelper;

class SyncController extends Controller
{
  protected $layout = false;
  protected $folder = false;

  private $integrationLogModel;
  private $integrationTaskModel;

  public function __construct()
  {
    parent::__construct();

    $this->integrationLogModel = new IntegrationLogModel();
    $this->integrationTaskModel = new IntegrationTaskModel();
  }

  public function sync(int $referenceType = 0)
  {
    $resultTasks = $this->integrationTaskModel->findNextTask($referenceType);

    if (empty($resultTasks)) {
      return ['neutral' => 'No records found'];
    }

    $response = [];
    $dataTask = ConversionHelper::jsonToArray($resultTasks['data'] ?? null);

    // Review - Create a setting
    $serviceTo = ServiceType::BRAAVO;

    if ($resultTasks['service'] === ServiceType::BLING and $resultTasks['type'] === ReferenceType::CATEGORY) {
      $response = (new BlingCategorySyncComponent())->syncToEcommerce($resultTasks['reference_id'], $dataTask);
    }

    if ($resultTasks['service'] === ServiceType::BLING and $resultTasks['type'] === ReferenceType::SUPPLIER) {
      $response = (new BlingSupplierSyncComponent())->syncToEcommerce($resultTasks['reference_id'], $dataTask);
    }

    if ($resultTasks['service'] === ServiceType::BLING and $resultTasks['type'] === ReferenceType::PRODUCT) {
      $response = (new BlingProductSyncComponent())->syncToEcommerce($resultTasks['reference_id'], $dataTask);
    }

    if ($resultTasks['service'] === ServiceType::BLING and $resultTasks['type'] === ReferenceType::SKU) {
      $response = (new BlingSkuSyncComponent())->syncToEcommerce($resultTasks['reference_id'], $dataTask);
    }

    if (empty($response)) {
      return ['neutral' => 'No records found'];
    }

    if (isset($response['error'])) {
      $this->integrationTaskModel->update($resultTasks['id'], [
        'attempts' => $resultTasks['attempts'] + 1,
        'request_body' => json_encode($response['error']['request_body']),
        'response_body' => json_encode($response['error']['response_body']),
      ]);

      return ['error' => 'It was not possible to send the registration ' . $resultTasks['id'] . ' to the platform'];
    }

    $this->integrationTaskModel->delete($resultTasks['id']);

    $this->integrationLogModel->createOrUpdate([
      'type' => $resultTasks['type'],
      'service_from' => $resultTasks['service'],
      'service_to' => $serviceTo,
      'reference_id' => $resultTasks['reference_id'],
      'request_body' => json_encode($response['success']['request_body']),
      'response_body' => json_encode($response['success']['response_body']),
    ]);

    return ['success' => true, 'total_synchronized' => 1];
  }
}