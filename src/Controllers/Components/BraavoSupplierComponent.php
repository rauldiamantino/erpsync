<?php

namespace App\Controllers\Components;

use App\Classes\Constants\Status;
use App\Controllers\Components\BraavoComponent;

class BraavoSupplierComponent extends BraavoComponent
{
  public function __construct()
  {
    parent::__construct();
  }

  public function sync(array $data): array
  {
    $missingFields = [];

    if (! isset($data['id']) or empty($data['id'])) {
      $missingFields[] = 'id';
    }

    if (! isset($data['name']) or empty($data['name'])) {
      $missingFields[] = 'name';
    }

    if ($missingFields) {
      return ['error' => 'Incomplete data. Missing fields: ' . implode(', ', $missingFields)];
    }

    $page = 1;
    $pageMax = 500;
    $suppliersIds = [];

    do {
      $body = [
        'atual' => $page,
        'limite' => 100,
      ];

      $response = $this->fetchAllBraavoSupplier($body);

      if (isset($response['error'])) {
        return $response;
      }

      $extractIds = $this->extractSupplierIds($response);
      $suppliersIds += $extractIds;

      $page++;
    }
    while(count($response) === 100 and $page <= $pageMax);

    $supplierName = $data['name'];
    $supplierId = $suppliersIds[ $data['name'] ] ?? '0';

    if (empty($supplierId)) {
      $response = $this->createSupplier($supplierName);

      if (isset($response['error'])) {
        return $response;
      }

      $supplierId = $response['id'];
    }

    return ['success' => true, 'supplierId' => $supplierId, 'categoryName' => $supplierName];
  }

  private function fetchAllBraavoSupplier(array $body): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $response = $this->sendRequest('get', '/fornecedores/buscar/todos', $headers, $body);

    if (! is_array($response) or empty($response)) {
      return ['error' => 'Failed to get all suppliers'];
    }

    if (isset($response['erro']['mensagem']) and $response['erro']['mensagem'] == 'Nenhum registro encontrado.') {
      return [];
    }

    if (isset($response['erro']) and $response['erro']) {
      return $response;
    }

    return $response['ok'];
  }

  private function extractSupplierIds(array $response): array
  {
    $suppliersIds = [];
    foreach ($response as $value):
      $id = $value['id'] ?? '';
      $name = $value['nome'] ?? '';

      if ($id and $name) {
        $suppliersIds[ $name ] = (int) $id;
      }
    endforeach;

    return $suppliersIds;
  }

  private function createSupplier(string $supplierName): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $body = [
      'pai_id' => '0',
      'ativo' => Status::ACTIVE,
      'nome' => $supplierName,
    ];

    $response = $this->sendRequest('post', '/fornecedores/adicionar', $headers, $body);

    if (isset($response['erro']) and $response['erro']) {
      return ['error' => $response];
    }

    if (! isset($response['ok']['id']) or empty($response['ok']['id'])) {
      return ['error' => 'Error creating supplier'];
    }

    return $response['ok'];
  }
}