<?php

namespace App\Controllers\Components;

use App\Models\IntegrationTaskModel;
use App\Classes\Constants\ServiceType;
use App\Classes\Constants\ReferenceType;
use App\Controllers\Components\BraavoComponent;
use App\Controllers\Components\BraavoCategoryComponent;
use App\Controllers\Components\BraavoSupplierComponent;

class BraavoProductComponent extends BraavoComponent
{
  private $braavoCategory;
  private $braavoSupplier;
  private $integrationTaskModel;

  public function __construct()
  {
    parent::__construct();

    $this->braavoCategory = new BraavoCategoryComponent();
    $this->braavoSupplier = new BraavoSupplierComponent();
    $this->integrationTaskModel = new IntegrationTaskModel();
  }

  public function sync(array $data): array
  {
    $missingFields = $this->checkMissingFields($data);

    if (isset($missingFields['error'])) {
      return ['error' => ['payload' => $data, 'response' => $missingFields['error']]];
    }

    $productExists = $this->checkProductExists($data['code']);

    if (isset($productExists['error'])) {
      return ['error' => ['payload' => $data, 'response' => $productExists['error']]];
    }

    if (isset($productExists['id'])) {
      return ['error' => ['payload' => $data, 'response' => 'The product already exists']];
    }

    $payload = $this->preparePayload($data);

    if (isset($payload['error'])) {
      return ['error' => ['payload' => $data, 'response' => $payload['error']]];
    }

    $response = $this->createProduct($payload);

    if (isset($response['error'])) {
      return ['error' => ['payload' => $payload, 'response' => $response['error']]];
    }

    $this->scheduleSkusTasks($data);

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

    if (! isset($data['length'])) {
      $missingFields[] = 'length';
    }

    if (! isset($data['width'])) {
      $missingFields[] = 'width';
    }

    if (! isset($data['height'])) {
      $missingFields[] = 'height';
    }

    if (! isset($data['shortDescription'])) {
      $missingFields[] = 'shortDescription';
    }

    if (! isset($data['id']) or empty($data['id'])) {
      $missingFields[] = 'id';
    }

    if (! isset($data['name']) or empty($data['name'])) {
      $missingFields[] = 'name';
    }

    if (! isset($data['code']) or empty($data['code'])) {
      $missingFields[] = 'code';
    }

    if (empty($missingFields)) {
      return [];
    }

    return ['error' => 'Incomplete data. Missing fields: ' . implode(', ', $missingFields)];
  }

  private function checkProductExists(string $code): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $body = [
      'ref' => $code,
    ];

    $response = $this->sendRequest('get', '/produtos/buscar/ref', $headers, $body);

    if (! is_array($response) or empty($response)) {
      return ['error' => 'Failed to check if product exists'];
    }

    if (isset($response['erro']['mensagem']) and $response['erro']['mensagem'] == 'Nenhum registro encontrado.') {
      return [];
    }

    if (isset($response['erro']) and $response['erro']) {
      return $response;
    }

    return $response['ok'];
  }

  private function preparePayload(array $data): array
  {
    $payload = [
      'categoria_id' => '0',
      'fornecedor_id' => '0',
      'marca_id' => '0',
      'tabela_id' => '0',
      'ativo' => '0',
      'nome' => $data['name'],
      'ref' => $data['code'],
      'ncm' => (string) $data['ncm'],
      'estrelas' => '5',
      'deduzir' => '1',
      'esgotado' => '1',
      'prazo' => '0',
      'profundidade' => number_format(floatval($data['length']), 1, '.'),
      'largura' => number_format(floatval($data['width']), 1, '.'),
      'altura' => number_format(floatval($data['height']), 1, '.'),
      'sexo' => '',
      'publico' => '',
      'descricao' => $data['shortDescription'],
      'youtube' => '',
    ];

    // Category
    $response = $this->getCategoryId($data);

    if (isset($response['error'])) {
      return $response;
    }

    $payload['categoria_id'] = strval($response['categoryId'] ?? 0);

    // Supplier
    $response = $this->getSupplierId($data);

    if (isset($response['error'])) {
      return $response;
    }

    $payload['fornecedor_id'] = strval($response['supplierId'] ?? 0);

    // Status
    if (strtoupper($data['status']) == 'A') {
      $payload['status'] = '1';
    }

    return $payload;
  }

  private function getCategoryId(array $data): array
  {
    if (! isset($data['categoryName']) or empty($data['categoryName'])) {
      return [];
    }

    $page = 1;
    $pageMax = 500;

    do {
      $body = [
        'atual' => $page,
        'limite' => 100,
      ];

      $response = $this->braavoCategory->fetchAllBraavoCategory($body);

      if (isset($response['error'])) {
        return $response;
      }

      foreach ($response as $value):

        if (! isset($value['id']) or empty($value['id'])) {
          continue;
        }

        if (isset($value['nome']) and $value['nome'] == $data['categoryName']) {
          return ['categoryId' => $value['id'] ];
        }
      endforeach;

      $page++;
    }
    while(count($response) === 100 and $page <= $pageMax);

    return [];
  }

  private function getSupplierId(array $data): array
  {
    if (! isset($data['supplierName']) or empty($data['supplierName'])) {
      return [];
    }

    $page = 1;
    $pageMax = 500;

    do {
      $body = [
        'atual' => $page,
        'limite' => 100,
      ];

      $response = $this->braavoSupplier->fetchAllBraavoSupplier($body);

      if (isset($response['error'])) {
        return $response;
      }

      foreach ($response as $value):

        if (! isset($value['id']) or empty($value['id'])) {
          continue;
        }

        if (isset($value['nome']) and $value['nome'] == $data['supplierName']) {
          return ['supplierId' => $value['id'] ];
        }
      endforeach;

      $page++;
    }
    while(count($response) === 100 and $page <= $pageMax);

    return [];
  }

  private function createProduct(array $payload): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $method = 'post';
    $body = $payload;

    if (isset($payload['id'])) {
      $method = 'put';
    }

    $response = $this->sendRequest($method, '/categorias/adicionar', $headers, $body);

    if (isset($response['erro']) and $response['erro']) {
      return ['error' => $response];
    }

    if (! isset($response['ok']['id']) or empty($response['ok']['id'])) {
      return ['error' => 'Error creating product'];
    }

    return $response['ok'];
  }

  private function scheduleSkusTasks($data)
  {
    if (! isset($data['skus'][0]['id'])) {
      return;
    }

    foreach ($data['skus'] as $value):

      if (isset($value['id']) and $value['id']) {
        $this->integrationTaskModel->scheduleTask(ReferenceType::SKU, ServiceType::BLING, $value['id']);
      }
    endforeach;
  }
}