<?php

namespace App\Controllers\Components;

use App\Models\IntegrationTaskModel;
use App\Controllers\Components\BraavoComponent;

class BraavoStockComponent extends BraavoComponent
{
  public function __construct()
  {
    parent::__construct();
  }

  public function sync(array $data, int $braavoSkuId): array
  {
    $missingFields = $this->checkMissingFields($data);

    if (isset($missingFields['error'])) {
      return $this->returnError($missingFields, $data);
    }

    $payload = $this->preparePayload($data, $braavoSkuId);

    if (isset($payload['error'])) {
      return $this->returnError($payload, $data);
    }

    $response = $this->createStock($payload);

    if (isset($response['error'])) {
      return $this->returnError($response, $payload);
    }

    return $this->returnSuccess($response, $payload);
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