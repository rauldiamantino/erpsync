<?php

namespace App\Controllers\Components;

use App\Classes\Constants\ServiceType;
use App\Classes\Constants\ReferenceType;

use App\Controllers\Components\BlingComponent;
use App\Models\IntegrationTaskModel;

class BlingProductSchedulerComponent extends BlingComponent
{
  private IntegrationTaskModel $integrationTaskModel;

  public function __construct(IntegrationTaskModel $integrationTaskModel)
  {
    parent::__construct();

    $this->integrationTaskModel = $integrationTaskModel;
  }

  public function scheduleSync(): array
  {
    $page = 1;
    $pageMax = 500;
    $productsIds = [];

    do {
      $queryParams = [
        'pagina' => $page,
        'limite' => 100,
        'tipo' => 'P',
        'criterio' => 5,
      ];

      $response = $this->fetchAllBlingProduct($queryParams);

      if (isset($response['error'])) {
        return $response;
      }

      $extractIds = $this->extractProductIds($response);
      $productsIds += $extractIds;

      $page++;
    }
    while(count($response) === 100 and $page <= $pageMax);

    return [
      'success' => true,
      'total_scheduled' => $this->scheduleTask($productsIds),
    ];
  }

  private function fetchAllBlingProduct(array $queryParams): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $response = $this->sendRequest('get', '/produtos', $headers, null, $queryParams);

    if (! is_array($response) or empty($response)) {
      return ['error' => 'Failed to get all products'];
    }

    if (isset($response['error']) and $response['error']) {
      return $response;
    }

    if (! isset($response['data'][0]['id'])) {
      return ['error' => 'Products not found'];
    }

    return $response['data'];
  }

  private function extractProductIds(array $response): array
  {
    $productsIds = [];
    foreach ($response as $value):
      $id = $value['id'] ?? '';
      $situation = $value['situacao'] ?? '';

      if ($id and in_array($situation, ['A', 'I'])) {
        $productsIds[ $id ] = (int) $id;
      }
    endforeach;

    return $productsIds;
  }

  private function scheduleTask(array $productsIds): int
  {
    if (empty($productsIds)) {
      return 0;
    }

    foreach ($productsIds as $productId):
      $this->integrationTaskModel->scheduleTask(ReferenceType::PRODUCT, ServiceType::BLING, $productId);
    endforeach;

    return count($productsIds);
  }
}