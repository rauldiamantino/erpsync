<?php

namespace App\Controllers\Components;

use App\Classes\Config;
use App\Controllers\Components\BlingComponent;
use App\Controllers\Components\BraavoStockComponent;

class BlingStockSyncComponent extends BlingComponent
{
  public function syncToEcommerce(int $id, array $dataTask): array
  {
    if (empty($id)) {
      return $this->returnError('Empty sku ID');
    }

    $response = $this->fetchBlingStock($id);

    if (isset($response['error'])) {
      return $this->returnError($response);
    }

    $stock = [
      'id' => $response['produto']['id'] ?? '',
      'code' => $response['produto']['codigo'] ?? '',
      'stock' => $response['saldoVirtualTotal'] ?? 0,
    ];

    $braavoSkuId = $dataTask['braavoSkuId'] ?? '';
    $braavoSkuId = (int) $braavoSkuId;

    $braavoComponent = new BraavoStockComponent();
    $responsePlatform = $braavoComponent->sync($stock, $braavoSkuId);

    if (isset($responsePlatform['error'])) {
      return $this->returnError($responsePlatform['error']['response'], $responsePlatform['error']['payload']);
    }

    return $this->returnSuccess($responsePlatform['success']['response'], $responsePlatform['success']['payload']);
  }

  private function fetchBlingStock(int $id): array
  {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ];

    $queryParams = [
      'idsProdutos' => [ $id ],
    ];

    $depositId = (int) Config::get('bling_deposit_id');

    if (empty($depositId)) {
      return ['error' => 'Empty deposit ID'];
    }

    $response = $this->sendRequest('get', '/estoques/saldos/' . $depositId, $headers, null, $queryParams);

    if (! is_array($response) or empty($response)) {
      return ['error' => 'Failed to get sku stock'];
    }

    if (isset($response['error']) and $response['error']) {
      return $response;
    }

    if (! isset($response['data'][0]['saldoVirtualTotal'])) {
      return [];
    }

    return $response['data'][0];
  }
}