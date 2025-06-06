<?php

namespace App\Controllers\Components;

use App\Controllers\Components\BraavoComponent;

class BraavoVariationComponent extends BraavoComponent
{
  public function sync(array $data)
  {
    $page = 1;
    $pageMax = 500;
    $variationsIds = [];

    do {
      $body = [
        'atual' => $page,
        'limite' => 100,
      ];

      $response = $this->fetchAllBraavoVariation($body);

      if (isset($response['error'])) {
        return $this->returnError($response);
      }

      $extractIds = $this->extractVariationIds($response);
      $variationsIds += $extractIds;

      $page++;
    }
    while(count($response) === 100 and $page <= $pageMax);

    $variation1Name = reset($data);
    $variation1Type = array_key_first($data);
    $variation2Name = '';
    $variation2Type = '';

    if (count($data) == 2) {
      $variation2Name = end($data);
      $variation2Type = array_key_last($data);
    }

    // Variation TypeID
    $responseVar1Type = $this->createVariationType($variation1Type, $variationsIds);

    if (isset($responseVar1Type['error'])) {
      return $this->returnError($responseVar1Type);
    }

    $responseVar2Type = $this->createVariationType($variation2Type, $variationsIds);

    if (isset($responseVar2Type['error'])) {
      return $this->returnError($responseVar2Type);
    }

    $variation1TypeId = $responseVar1Type['id'] ?? '';
    $variation2TypeId = $responseVar2Type['id'] ?? '';

    $variation1TypeId = (int) $variation1TypeId;
    $variation2TypeId = (int) $variation2TypeId;

    // Variation ID
    $responseVar1 = $this->createVariation($variation1Name, $variation1TypeId, $variationsIds);

    if (isset($responseVar1['error'])) {
      return $this->returnError($responseVar1);
    }

    $responseVar2 = $this->createVariation($variation2Name, $variation2TypeId, $variationsIds);

    if (isset($responseVar2['error'])) {
      return $this->returnError($responseVar2);
    }

    return $this->returnSuccess(['variation1Id' => $responseVar1['id'] ?? 0, 'variation2Id' => $responseVar2['id'] ?? 0]);
  }

  private function fetchAllBraavoVariation(array $body): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $response = $this->sendRequest('get', '/variacoes/buscar/todos', $headers, $body);

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

  private function extractVariationIds(array $response): array
  {
    $variationsIds = [];
    foreach ($response as $value):
      $id = $value['id'] ?? '';
      $name = strtolower($value['nome'] ?? '');
      $parentId = $value['pai_id'] ?? '';

      if ($id and $name) {
        $variationsIds[ $name ][] = ['id' => (int) $id, 'parentId' => (int) $parentId];
      }
    endforeach;

    return $variationsIds;
  }

  private function createVariationType(?string $variationType, array $variationsIds): array
  {
    if (empty($variationType)) {
      return [];
    }

    foreach ($variationsIds as $keyVariationsIds => $valueVariationsIds):

      if ($keyVariationsIds != strtolower($variationType)) {
        continue;
      }

      foreach ($valueVariationsIds as $value):

        if (! isset($value['id']) or empty($value['id'])) {
          continue;
        }

        if (! isset($value['parentId']) or empty($value['parentId'])) {
          return ['id' => $value['id']];
        }
      endforeach;
    endforeach;

    $payload = [
      'pai_id' => '0',
      'ativo' => '1',
      'nome' => $variationType,
    ];

    $response = $this->createVariationApi($payload);

    if (isset($response['error'])) {
      return $response;
    }

    return ['id' => $response['id']];
  }

  private function createVariation(?string $variation, ?int $variationTypeId, array $variationsIds): array
  {
    if (empty($variation) or empty($variationTypeId)) {
      return [];
    }

    foreach ($variationsIds as $keyVariationsIds => $valueVariationsIds):

      if ($keyVariationsIds != strtolower($variation)) {
        continue;
      }

      foreach ($valueVariationsIds as $value):

        if (! isset($value['id']) or empty($value['id'])) {
          continue;
        }

        if (isset($value['parentId']) and $value['parentId'] == $variationTypeId) {
          return ['id' => $value['id']];
        }
      endforeach;
    endforeach;

    $payload = [
      'pai_id' => (string) $variationTypeId,
      'ativo' => '1',
      'nome' => $variation,
    ];

    $response = $this->createVariationApi($payload);

    if (isset($response['error'])) {
      return $response;
    }

    return ['id' => $response['id']];
  }

  private function createVariationApi(array $payload): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $response = $this->sendRequest('post', '/variacoes/adicionar', $headers, $payload);

    if (isset($response['erro']) and $response['erro']) {
      return ['error' => $response];
    }

    if (! isset($response['ok']['id']) or empty($response['ok']['id'])) {
      return ['error' => 'Error creating variation'];
    }

    return $response['ok'];
  }
}