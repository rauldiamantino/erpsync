<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\IntegrationLogModel;
use App\Models\IntegrationTaskModel;
use App\Classes\Constants\ServiceType;
use App\Classes\Constants\ReferenceType;
use App\Controllers\Components\BlingCategorySyncComponent;

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

  public function sync(int $referenceType)
  {
    $resultTasks = $this->integrationTaskModel->findNextTask($referenceType);

    if (empty($resultTasks)) {
      return ['neutral' => 'No records found'];
    }

    $response = [];

    if ($resultTasks['service'] === ServiceType::BLING and $resultTasks['type'] === ReferenceType::CATEGORY) {
      $response = (new BlingCategorySyncComponent())->syncToEcommerce($resultTasks['reference_id']);
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
      'service' => $resultTasks['service'],
      'reference_id' => $resultTasks['reference_id'],
      'request_body' => json_encode($response['success']['request_body']),
      'response_body' => json_encode($response['success']['response_body']),
    ]);

    return ['success' => true, 'total_synchronized' => 1];
  }
}