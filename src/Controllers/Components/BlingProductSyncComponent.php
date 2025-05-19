<?php

namespace App\Controllers\Components;

use App\Controllers\Components\BlingComponent;

class BlingProductSyncComponent extends BlingComponent
{
  public function syncToEcommerce(int $id): array
  {
    if (empty($id)) {
      return ['error' => 'Empty product ID'];
    }

    $response = $this->fetchBlingProduct($id);

    if (isset($response['error'])) {
      return $response;
    }

    return $response;
  }

  private function fetchBlingProduct(int $id): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $response = $this->sendRequest('get', '/produtos/' . $id, $headers);

    if (! is_array($response) or empty($response)) {
      return ['error' => 'Failed to get product'];
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