<?php

namespace App\Controllers\Components;

use App\Controllers\Components\BlingComponent;

class BlingCategorySyncComponent extends BlingComponent
{
  public function syncToEcommerce(int $id): array
  {
    if (empty($id)) {
      return ['error' => 'Empty category ID'];
    }

    $response = $this->fetchBlingCategory($id);

    if (isset($response['error'])) {
      return $response;
    }

    return $response;
  }

  private function fetchBlingCategory(int $id): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $response = $this->sendRequest('get', '/categorias/produtos/' . $id, $headers);

    if (! is_array($response) or empty($response)) {
      return ['error' => 'Failed to get category'];
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