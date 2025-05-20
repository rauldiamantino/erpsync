<?php

namespace App\Controllers\Components;

use App\Classes\Constants\ServiceType;
use App\Classes\Constants\ReferenceType;

use App\Controllers\Components\BlingComponent;
use App\Models\IntegrationTaskModel;

class BlingCategorySchedulerComponent extends BlingComponent
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
    $categoriesIds = [];

    do {
      $queryParams = [
        'pagina' => $page,
        'limite' => 100,
      ];

      $response = $this->fetchAllBlingCategory($queryParams);

      if (isset($response['error'])) {
        return $response;
      }

      $extractIds = $this->extractCategoryIds($response);
      $categoriesIds += $extractIds;

      $page++;
    }
    while(count($response) === 100 and $page <= $pageMax);

    return [
      'success' => true,
      'total_scheduled' => $this->scheduleTask($categoriesIds),
    ];
  }

  private function fetchAllBlingCategory(array $queryParams): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $response = $this->sendRequest('get', '/categorias/produtos', $headers, null, $queryParams);

    if (! is_array($response) or empty($response)) {
      return ['error' => 'Failed to get all categories'];
    }

    if (isset($response['error']) and $response['error']) {
      return $response;
    }

    if (! isset($response['data'][0]['id'])) {
      return ['error' => 'Categories not found'];
    }

    return $response['data'];
  }

  private function extractCategoryIds(array $response): array
  {
    $categoriesIds = [];
    foreach ($response as $value):
      $id = $value['id'] ?? '';

      if ($id) {
        $categoriesIds[ $id ] = (int) $id;
      }
    endforeach;

    return $categoriesIds;
  }

  private function scheduleTask(array $categoriesIds): int
  {
    if (empty($categoriesIds)) {
      return 0;
    }

    foreach ($categoriesIds as $categoryId):
      $this->integrationTaskModel->scheduleTask(ReferenceType::CATEGORY, ServiceType::BLING, $categoryId);
    endforeach;

    return count($categoriesIds);
  }
}