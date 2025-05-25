<?php

namespace App\Controllers\Components;

use App\Controllers\Components\BlingComponent;
use App\Controllers\Components\BraavoSkuComponent;

class BlingSkuSyncComponent extends BlingComponent
{
  public function syncToEcommerce(int $id, array $dataTask): array
  {
    if (empty($id)) {
      return ['error' => ['request_body' => [], 'response_body' => 'Empty sku ID']];
    }

    $response = $this->fetchBlingSku($id);

    if (isset($response['error'])) {
      return ['error' => ['request_body' => [], 'response_body' => $response['error']]];
    }

    $sku = [
      'id' => $response['id'] ?? '',
      'code' => $response['codigo'] ?? '',
      'format' => strtoupper($response['formato'] ?? ''),
      'salePrice' => $response['preco'] ?? '',
      'costPrice' => $response['precoCusto'] ?? '',
      'stock' => $response['estoque']['saldoVirtualTotal'] ?? 0,
      'stockLocation' => $response['estoque']['localizacao'] ?? '',
      'status' => $response['situacao'] ?? '',
      'weight' => $response['pesoLiquido'] ?? 0.0,
      'ncm' => $response['tributacao']['ncm'] ?? '',
      'gtin' => $response['gtin'] ?? '',
      'variations' => $this->parseVariations($response),
    ];

    if ($sku['format'] != 'S') {
      return ['error' => ['request_body' => $sku, 'response_body' => 'Invalid format']];
    }

    $braavoProductId = $dataTask['braavoProductId'] ?? '';
    $braavoProductId = (int) $braavoProductId;

    $braavoComponent = new BraavoSkuComponent();
    $responsePlatform = $braavoComponent->sync($sku, $braavoProductId);

    if (isset($responsePlatform['error'])) {
      return ['error' => ['request_body' => $responsePlatform['error']['payload'], 'response_body' => $responsePlatform['error']['response']]];
    }

    return ['success' => ['request_body' => $responsePlatform['success']['payload'], 'response_body' => $responsePlatform['success']['response']]];
  }

  private function parseVariations(array $data): array
  {
    $variationApi = $data['variacao']['nome'] ?? '';
    $variationArray = explode(';', $variationApi);

    $result = [];
    foreach ($variationArray as $value):
      $array = explode(':', $value);

      if (count($array) == 2) {
        $result[ $array[0] ] = $array[1];
      }
    endforeach;

    return $result;
  }

  private function fetchBlingSku(int $id): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $response = $this->sendRequest('get', '/produtos/' . $id, $headers);

    if (! is_array($response) or empty($response)) {
      return ['error' => 'Failed to get sku'];
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