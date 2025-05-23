<?php

namespace App\Controllers\Components;

use App\Controllers\Components\BlingComponent;
use App\Controllers\Components\BraavoSupplierComponent;

class BlingSupplierSyncComponent extends BlingComponent
{
  public function syncToEcommerce(int $id): array
  {
    if (empty($id)) {
      return ['error' => ['request_body' => [], 'response_body' => 'Empty supplier ID']];
    }

    $response = $this->fetchBlingSupplier($id);

    if (isset($response['error'])) {
      return ['error' => ['request_body' => [], 'response_body' => $response['error']]];
    }

    $supplier = [
      'id' => intval($response['id'] ?? 0),
      'name' => $response['nome'] ?? '',
    ];

    $braavoComponent = new BraavoSupplierComponent();
    $responsePlatform = $braavoComponent->sync($supplier);

    if (isset($responsePlatform['error'])) {
      return ['error' => ['request_body' => $supplier, 'response_body' => $responsePlatform['error']]];
    }

    return ['success' => ['request_body' => $supplier, 'response_body' => $responsePlatform]];
  }

  private function fetchBlingSupplier(int $id): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $queryParams = [
      'pagina' => 1,
      'limite' => 1,
      'idsContatos' => [ $id ],
    ];

    $response = $this->sendRequest('get', '/contatos', $headers, null, $queryParams);

    if (! is_array($response) or empty($response)) {
      return ['error' => 'Failed to get supplier'];
    }

    if (isset($response['error']) and $response['error']) {
      return $response;
    }

    if (! isset($response['data'][0]['id'])) {
      return [];
    }

    return $response['data'][0];
  }
}