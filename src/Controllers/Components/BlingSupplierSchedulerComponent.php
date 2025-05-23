<?php

namespace App\Controllers\Components;

use App\Classes\Config;
use App\Models\IntegrationTaskModel;

use App\Classes\Constants\ServiceType;
use App\Classes\Constants\ReferenceType;
use App\Controllers\Components\BlingComponent;

class BlingSupplierSchedulerComponent extends BlingComponent
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
    $suppliersIds = [];

    $typeContactId = (int) Config::get('bling_supplier_contact_type');

    if (empty($typeContactId)) {
      return ['error' => 'Empty type contact ID'];
    }

    do {
      $queryParams = [
        'pagina' => $page,
        'limite' => 100,
        'idTipoContato' => $typeContactId,
        'criterio' => 3,
      ];

      $response = $this->fetchAllBlingSupplier($queryParams);

      if (isset($response['error'])) {
        return $response;
      }

      $extractIds = $this->extractSupplierIds($response);
      $suppliersIds += $extractIds;

      $page++;
    }
    while(count($response) === 100 and $page <= $pageMax);

    return [
      'success' => true,
      'total_scheduled' => $this->scheduleTask($suppliersIds),
    ];
  }

  private function fetchAllBlingSupplier(array $queryParams): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $response = $this->sendRequest('get', '/contatos', $headers, null, $queryParams);

    if (! is_array($response) or empty($response)) {
      return ['error' => 'Failed to get all suppliers'];
    }

    if (isset($response['error']) and $response['error']) {
      return $response;
    }

    if (! isset($response['data'][0]['id'])) {
      return ['error' => 'Suppliers not found'];
    }

    return $response['data'];
  }

  private function extractSupplierIds(array $response): array
  {
    $suppliersIds = [];
    foreach ($response as $value):
      $id = $value['id'] ?? '';
      $id = (int) $id;

      $nome = $value['nome'] ?? '';

      if ($id and $nome) {
        $suppliersIds[ $id ] = $id;
      }
    endforeach;

    return $suppliersIds;
  }

  private function scheduleTask(array $suppliersIds): int
  {
    if (empty($suppliersIds)) {
      return 0;
    }

    foreach ($suppliersIds as $supplierId):
      $this->integrationTaskModel->scheduleTask(ReferenceType::SUPPLIER, ServiceType::BLING, $supplierId);
    endforeach;

    return count($suppliersIds);
  }
}