<?php

namespace App\Controllers\Components;

use App\Controllers\Components\BlingComponent;
use App\Controllers\Components\BraavoSkuComponent;

class BlingSkuSyncComponent extends BlingComponent
{
  public function syncToEcommerce(int $id, array $dataTask): array
  {
    if (empty($id)) {
      return $this->returnError('Empty sku ID');
    }

    $response = $this->fetchBlingSku($id);

    if (isset($response['error'])) {
      return $this->returnError($response);
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
      return $this->returnError('Invalid format', $sku);
    }

    $braavoProductId = $dataTask['braavoProductId'] ?? '';
    $braavoProductId = (int) $braavoProductId;

    $braavoComponent = new BraavoSkuComponent();
    $responsePlatform = $braavoComponent->sync($sku, $braavoProductId);

    if (isset($responsePlatform['error'])) {
      return $this->returnError($responsePlatform['error']['response_body'], $responsePlatform['error']['request_body']);
    }

    return $this->returnSuccess($responsePlatform['success']['response_body'], $responsePlatform['success']['request_body']);
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