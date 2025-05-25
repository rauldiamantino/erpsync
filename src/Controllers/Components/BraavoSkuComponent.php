<?php

namespace App\Controllers\Components;

use App\Models\IntegrationTaskModel;
use App\Classes\Constants\ReferenceType;
use App\Controllers\Components\BraavoComponent;
use App\Controllers\Components\BraavoVariationComponent;

class BraavoSkuComponent extends BraavoComponent
{
  private $integrationTaskModel;
  private $braavoVariationComponent;

  public function __construct()
  {
    parent::__construct();

    $this->integrationTaskModel = new IntegrationTaskModel();
    $this->braavoVariationComponent = new BraavoVariationComponent();
  }

  public function sync(array $data, int $braavoProductId): array
  {
    $missingFields = $this->checkMissingFields($data);

    if (isset($missingFields['error'])) {
      return ['error' => ['payload' => $data, 'response' => $missingFields['error']]];
    }

    $skuExists = $this->checkSkuExists($data['code']);

    if (isset($skuExists['error'])) {
      return ['error' => ['payload' => $data, 'response' => $skuExists['error']]];
    }

    if (isset($skuExists['id'])) {
      return ['error' => ['payload' => $data, 'response' => 'The sku already exists']];
    }

    $payload = $this->preparePayload($data, $braavoProductId);

    if (isset($payload['error'])) {
      return ['error' => ['payload' => $data, 'response' => $payload['error']]];
    }

    $response = $this->createSku($payload);

    if (isset($response['error'])) {
      return ['error' => ['payload' => $payload, 'response' => $response['error']]];
    }

    $this->scheduleEstoquesTasks($data, $response);

    return ['success' => ['payload' => $payload, 'response' => $response]];
  }

  private function checkMissingFields(array $data): array
  {
    $missingFields = [];

    if (! isset($data['status'])) {
      $missingFields[] = 'status';
    }

    if (! isset($data['ncm'])) {
      $missingFields[] = 'ncm';
    }

    if (! isset($data['id']) or empty($data['id'])) {
      $missingFields[] = 'id';
    }

    if (! isset($data['code']) or empty($data['code'])) {
      $missingFields[] = 'code';
    }

    if (empty($missingFields)) {
      return [];
    }

    return ['error' => 'Incomplete data. Missing fields: ' . implode(', ', $missingFields)];
  }

  private function checkSkuExists(string $code): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $body = [
      'ref' => $code,
    ];

    $response = $this->sendRequest('get', '/skus/buscar/ref', $headers, $body);

    if (! is_array($response) or empty($response)) {
      return ['error' => 'Failed to check if sku exists'];
    }

    if (isset($response['erro']['mensagem']) and $response['erro']['mensagem'] == 'Nenhum registro encontrado.') {
      return [];
    }

    if (isset($response['erro']) and $response['erro']) {
      return $response;
    }

    return $response['ok'] ?? [];
  }

  private function preparePayload(array $data, int $braavoProductId): array
  {
    if (empty($braavoProductId)) {
      return ['error' => 'Empty product ID'];
    }

    $payload = [
      'produto_id' => (string) $braavoProductId,
      'var1_id' => '0',
      'var2_id' => '0',
      'ativo' => '0',
      'ref' => $data['code'],
      'gtin' => $data['gtin'],
      'mpn' => '',
      'peso' => $data['weight'],
      'preco_custo' => $data['costPrice'],
      'preco_original' => '0.00',
      'preco_venda' => $data['salePrice'],
      'movimentacao' => 'entrada',
      'lancamento' => '0'
    ];

    // Variations
    $response = $this->braavoVariationComponent->sync($data['variations']);

    if (isset($response['error'])) {
      return $response;
    }

    $payload['var1_id'] = strval($response['variation1Id'] ?? 0);
    $payload['var2_id'] = strval($response['variation2Id'] ?? 0);

    // Status
    if (strtoupper($data['status']) == 'A') {
      $payload['ativo'] = '1';
    }

    return $payload;
  }

  private function createSku(array $payload): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $response = $this->sendRequest('post', '/skus/adicionar', $headers, $payload);

    if (isset($response['erro']) and $response['erro']) {
      return ['error' => $response];
    }

    if (! isset($response['ok']['id']) or empty($response['ok']['id'])) {
      return ['error' => 'Error creating sku'];
    }

    return $response['ok'];
  }

  private function scheduleEstoquesTasks($data, $response): void
  {
    if (! isset($data['id'])) {
      return;
    }

    $braavoSkuId = $response['id'] ?? 0;
    $braavoSkuId = (int) $braavoSkuId;

    $dataTask = [
      'braavoSkuId' => $braavoSkuId,
    ];

    $this->integrationTaskModel->scheduleTask(ReferenceType::STOCK, ServiceType::BLING, $data['id'], $dataTask);
  }
}