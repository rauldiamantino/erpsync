<?php

namespace App\Controllers\Components;

use App\Controllers\Components\BlingComponent;
use App\Controllers\Components\BraavoCategoryComponent;

class BlingCategorySyncComponent extends BlingComponent
{
  public function __construct()
  {
    parent::__construct();
  }

  public function syncToEcommerce(int $id): array
  {
    if (empty($id)) {
      return ['error' => ['request_body' => [], 'response_body' => 'Empty category ID']];
    }

    $response = $this->fetchBlingCategory($id);

    if (isset($response['error'])) {
      return ['error' => ['request_body' => [], 'response_body' => $response['error']]];
    }

    $category = [
      'id' => intval($response['id'] ?? 0),
      'name' => $response['descricao'] ?? '',
      'parentId' => intval($response['categoriaPai']['id'] ?? 0),
      'parentName' => '',
    ];

    $response = $this->getParentCategoryName($category);

    if (isset($response['error'])) {
      return ['error' => ['request_body' => $category, 'response_body' => $response['error']]];
    }

    $category['parentName'] = $response['descricao'] ?? '';

    $braavoComponent = new BraavoCategoryComponent();
    $responsePlatform = $braavoComponent->sync($category);

    if (isset($responsePlatform['error'])) {
      return ['error' => ['request_body' => $category, 'response_body' => $responsePlatform['error']]];
    }

    return ['success' => ['request_body' => $category, 'response_body' => $responsePlatform]];
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

  private function getParentCategoryName(array $category): array
  {
    if (empty($category['parentId'])) {
      return [];
    }

    $response = $this->fetchBlingCategory($category['parentId']);

    if (isset($response['error'])) {
      return ['error' => ['response' => $response['error'], 'obs' => 'Failed to get parent category name']];
    }

    return $response;
  }
}