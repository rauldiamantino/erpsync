<?php

namespace App\Controllers\Components;

use App\Controllers\Components\BlingComponent;

class BlingSupplierSyncComponent extends BlingComponent
{
  public function syncToEcommerce(int $id): array
  {
    if (empty($id)) {
      return ['error' => 'Empty supplier ID'];
    }

    $response = $this->fetchBlingSupplier($id);

    if (isset($response['error'])) {
      return $response;
    }

    return $response;
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
      'idTipoContato' => [ $id ],
    ];

    $response = $this->sendRequest('get', '/contatos', $headers, null, $queryParams);

    if (! is_array($response) or empty($response)) {
      return ['error' => 'Failed to get supplier'];
    }

    if (isset($response['error']) and $response['error']) {
      return $response;
    }

    if (! isset($response['data']['id'])) {
      return [];
    }

    return $response['data'];
  }
}