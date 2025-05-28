<?php

namespace App\Controllers;

use App\Helpers\TypeHelper;
use App\Controllers\Controller;
use App\Helpers\ConversionHelper;
use App\Models\IntegrationLogModel;
use App\Models\IntegrationTaskModel;
use App\Classes\Constants\ServiceType;
use App\Classes\Constants\ReferenceType;
use App\Controllers\Components\BlingSkuSyncComponent;
use App\Controllers\Components\BlingStockSyncComponent;
use App\Controllers\Components\BlingProductSyncComponent;
use App\Controllers\Components\BlingCategorySyncComponent;
use App\Controllers\Components\BlingSupplierSyncComponent;
use App\Controllers\Components\BlingProductSchedulerComponent;
use App\Controllers\Components\BlingCategorySchedulerComponent;
use App\Controllers\Components\BlingSupplierSchedulerComponent;

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

  public function syncSend(int $referenceType = 0)
  {
    $resultTasks = $this->integrationTaskModel->findNextTask($referenceType);

    if (empty($resultTasks)) {
      return [
        'type' => 'neutral',
        'attempt' => 0,
        'message' => 'No records found',
        'taskId' => null,
      ];
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

    if ($resultTasks['service'] === ServiceType::BLING and $resultTasks['type'] === ReferenceType::STOCK) {
      $response = (new BlingStockSyncComponent())->syncToEcommerce($resultTasks['reference_id'], $dataTask);
    }

    if (empty($response)) {
      return [
        'type' => 'neutral',
        'attempt' => 0,
        'message' => 'No records found',
        'taskId' => $resultTasks['id'],
        'referenceType' => TypeHelper::getReferenceName($resultTasks['type']),
        'serviceFrom' => TypeHelper::getServiceName($resultTasks['service']),
        'serviceTo' => TypeHelper::getServiceName($serviceTo),
      ];
    }

    if (isset($response['error'])) {
      $this->integrationTaskModel->update($resultTasks['id'], [
        'attempts' => $resultTasks['attempts'] + 1,
        'request_body' => json_encode($response['error']['request_body']),
        'response_body' => json_encode($response['error']['response_body']),
      ]);

      return [
        'type' => 'error',
        'attempt' => $resultTasks['attempts'] + 1,
        'message' => 'It was not possible to send the registration to the platform',
        'taskId' => $resultTasks['id'],
        'referenceType' => TypeHelper::getReferenceName($resultTasks['type']),
        'serviceFrom' => TypeHelper::getServiceName($resultTasks['service']),
        'serviceTo' => TypeHelper::getServiceName($serviceTo),
      ];
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

    return [
      'type' => 'success',
      'attempt' => $resultTasks['attempts'] + 1,
      'message' => 'Synchronized',
      'taskId' => $resultTasks['id'],
      'referenceType' => TypeHelper::getReferenceName($resultTasks['type']),
      'serviceFrom' => TypeHelper::getServiceName($resultTasks['service']),
      'serviceTo' => TypeHelper::getServiceName($serviceTo),
    ];
  }

  public function syncReceive(int $referenceType): array
  {
    if ($referenceType === ReferenceType::CATEGORY) {
      $response = (new BlingCategorySchedulerComponent($this->integrationTaskModel))->scheduleSync();
    }

    if ($referenceType === ReferenceType::SUPPLIER) {
      $response = (new BlingSupplierSchedulerComponent($this->integrationTaskModel))->scheduleSync();
    }

    if ($referenceType === ReferenceType::PRODUCT) {
      $response = (new BlingProductSchedulerComponent($this->integrationTaskModel))->scheduleSync();
    }

    $referenceName = TypeHelper::getReferenceName($referenceType, true);
    $referenceName = strtolower($referenceName);

    if (isset($response['error']) or ! isset($response['total_scheduled'])) {
      return [
        'type' => 'error',
        'message' => 'Unable to receive ' . $referenceName,
      ];
    }

    return [
      'type' => 'success',
      'message' => $response['total_scheduled'] . ' ' . $referenceName . ' reiceved',
    ];
  }
}