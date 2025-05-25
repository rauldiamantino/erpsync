<?php

namespace App\Controllers\Components;

use App\Classes\Constants\Status;
use App\Controllers\Components\BraavoComponent;

class BraavoCategoryComponent extends BraavoComponent
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

    if (! isset($data['parentId'])) {
      $missingFields[] = 'parentId';
    }

    if (! isset($data['parentName'])) {
      $missingFields[] = 'parentName';
    }

    if ($missingFields) {
      return ['error' => 'Incomplete data. Missing fields: ' . implode(', ', $missingFields)];
    }

    $page = 1;
    $pageMax = 500;
    $categoriesIds = [];

    do {
      $body = [
        'atual' => $page,
        'limite' => 100,
      ];

      $response = $this->fetchAllBraavoCategory($body);

      if (isset($response['error'])) {
        return $response;
      }

      $extractIds = $this->extractCategoryIds($response);
      $categoriesIds += $extractIds;

      $page++;
    }
    while(count($response) === 100 and $page <= $pageMax);

    $parentName = $data['parentName'];
    $result = $this->createParentCategory($parentName, $categoriesIds);

    if (isset($result['error'])) {
      return $result;
    }

    $categoryName = $data['name'];
    $categoryId = $categoriesIds[ $data['name'] ] ?? '0';
    $parentId = $result['parentId'] ?? '0';

    $result = [
      'categoryId' => $categoryId,
      'categoryName' => $categoryName,
      'parentId' => $parentId,
      'obs' => 'The category already exists',
    ];

    if (empty($categoryId)) {
      $response = $this->createCategory($categoryName, $parentId);

      if (isset($response['error'])) {
        return $response;
      }

      $result = $response;
      $categoryId = $response['id'];
    }

    if ($parentId and $categoryId) {
      $response = $this->updateCategoryLink($categoryId, $parentId);

      if (isset($response['error'])) {
        return $response;
      }

      $result['updateLink'] = $response;
    }

    return $result;
  }

  public function fetchAllBraavoCategory(array $body): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $response = $this->sendRequest('get', '/categorias/buscar/todos', $headers, $body);

    if (! is_array($response) or empty($response)) {
      return ['error' => 'Failed to get all categories'];
    }

    if (isset($response['erro']['mensagem']) and $response['erro']['mensagem'] == 'Nenhum registro encontrado.') {
      return [];
    }

    if (isset($response['erro']) and $response['erro']) {
      return $response;
    }

    return $response['ok'] ?? [];
  }

  private function extractCategoryIds(array $response): array
  {
    $categoriesIds = [];
    foreach ($response as $value):
      $id = $value['id'] ?? '';
      $name = $value['nome'] ?? '';

      if ($id and $name) {
        $categoriesIds[ $name ] = (int) $id;
      }
    endforeach;

    return $categoriesIds;
  }

  private function createParentCategory(string $parentName, array $categoriesIds): array
  {
    if (empty($parentName)) {
      return [];
    }

    $parentId = $categoriesIds[ $parentName ] ?? '0';
    $parentId = (string) $parentId;


    if ($parentId) {
      return ['parentId' => $parentId ];
    }

    $response = $this->createCategory($parentName);

    if (isset($response['error'])) {
      return $response;
    }

    return ['parentId' => $response['id'] ];
  }

  private function createCategory(string $categoryName, string $parentId = '0'): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $body = [
      'pai_id' => (string) $parentId,
      'ativo' => Status::ACTIVE,
      'data_1' => '2010-01-01',
      'data_2' => '2050-12-31',
      'nome' => $categoryName,
      'google_cate' => 0,
    ];

    $response = $this->sendRequest('post', '/categorias/adicionar', $headers, $body);

    if (isset($response['erro']) and $response['erro']) {
      return ['error' => $response];
    }

    if (! isset($response['ok']['id']) or empty($response['ok']['id'])) {
      return ['error' => 'Error creating category'];
    }

    return $response['ok'];
  }

  private function updateCategoryLink(string $categoryId, string $parentId): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $body = [
      'id' => (string) $categoryId,
      'pai_id' => (string) $parentId,
      'ativo' => Status::ACTIVE,
    ];

    $response = $this->sendRequest('put', '/categorias/editar', $headers, $body);

    if (isset($response['erro']) and $response['erro']) {
      return ['error' => $response];
    }

    if (! isset($response['ok']['id']) or empty($response['ok']['id'])) {
      return ['error' => 'Error updating category link'];
    }

    return $response['ok'];
  }
}