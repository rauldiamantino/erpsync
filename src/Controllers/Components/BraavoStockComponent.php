<?php

namespace App\Controllers\Components;

use App\Models\IntegrationTaskModel;
use App\Controllers\Components\BraavoComponent;

class BraavoStockComponent extends BraavoComponent
{
  private $integrationTaskModel;

  public function __construct()
  {
    parent::__construct();

    $this->integrationTaskModel = new IntegrationTaskModel();
  }

  public function sync(array $data, int $braavoSkuId): array
  {
    $missingFields = $this->checkMissingFields($data);

    if (isset($missingFields['error'])) {
      return ['error' => ['payload' => $data, 'response' => $missingFields['error']]];
    }

    $payload = $this->preparePayload($data, $braavoSkuId);

    if (isset($payload['error'])) {
      return ['error' => ['payload' => $data, 'response' => $payload['error']]];
    }

    $response = $this->createStock($payload);

    if (isset($response['error'])) {
      return ['error' => ['payload' => $payload, 'response' => $response['error']]];
    }

    return ['success' => ['payload' => $payload, 'response' => $response]];
  }

  private function checkMissingFields(array $data): array
  {
    $missingFields = [];

    if (! isset($data['id']) or empty($data['id'])) {
      $missingFields[] = 'id';
    }

    if (! isset($data['code']) or empty($data['code'])) {
      $missingFields[] = 'code';
    }

    if (! isset($data['stock'])) {
      $missingFields[] = 'stock';
    }

    if (empty($missingFields)) {
      return [];
    }

    return ['error' => 'Incomplete data. Missing fields: ' . implode(', ', $missingFields)];
  }

  private function preparePayload(array $data, int $braavoSkuId): array
  {
    if (empty($braavoSkuId)) {
      return ['error' => 'Empty sku ID'];
    }

    $payload = [
      'sku_id' => (string) $braavoSkuId,
      'movimentacao' => 'balanco',
      'lancamento' => '0',
    ];

    if (isset($data['stock']) and is_numeric($data['stock'])) {
      $payload['lancamento'] = (string) $data['stock'];
    }

    return $payload;
  }

  private function createStock(array $payload): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $response = $this->sendRequest('put', '/estoques/atualizar', $headers, $payload);

    if (isset($response['erro']) and $response['erro']) {
      return ['error' => $response];
    }

    if (! isset($response['ok']['id']) or empty($response['ok']['id'])) {
      return ['error' => 'Error creating stock'];
    }

    return $response['ok'];
  }
}