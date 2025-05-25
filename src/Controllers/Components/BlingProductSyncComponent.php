<?php

namespace App\Controllers\Components;

use App\Controllers\Components\BlingComponent;
use App\Controllers\Components\BraavoProductComponent;

class BlingProductSyncComponent extends BlingComponent
{
  public function syncToEcommerce(int $id, array $data): array
  {
    if (empty($id)) {
      return ['error' => ['request_body' => [], 'response_body' => 'Empty product ID']];
    }

    $response = $this->fetchBlingProduct($id);

    if (isset($response['error'])) {
      return ['error' => ['request_body' => [], 'response_body' => $response['error']]];
    }

    $product = [
      'id' => $response['id'] ?? '',
      'name' => $response['nome'] ?? '',
      'code' => $response['codigo'] ?? '',
      'price' => $response['preco'] ?? 0,
      'stock' => $response['estoque']['saldoVirtualTotal'] ?? 0,
      'stockLocation' => $response['estoque']['localizacao'] ?? '',
      'status' => $response['situacao'] ?? '',
      'format' => strtoupper($response['formato'] ?? ''),
      'shortDescription' => $response['descricaoCurta'] ?? '',
      'additionalDescription' => $response['descricaoComplementar'] ?? '',
      'unit' => $response['unidade'] ?? '',
      'weight' => $response['pesoLiquido'] ?? 0.0,
      'brand' => $response['marca'] ?? '',
      'observations' => $response['obsercacoes'] ?? '',
      'categoryName' => '',
      'supplierName' => $response['fornecedor']['contato']['nome'] ?? '',
      'width' => $response['dimensoes']['largura'] ?? 0,
      'height' => $response['dimensoes']['altura'] ?? 0,
      'length' => $response['dimensoes']['profundidade'] ?? 0,
      'ncm' => $response['tributacao']['ncm'] ?? '',
      'gtin' => $response['tributacao']['gtin'] ?? '',
      'skus' => [],
    ];

    if (isset($response['categoria']['id']) and $response['categoria']['id']) {
      $responseCategoryName = $this->getCategoryName(intval($response['categoria']['id']));

      if (isset($responseCategoryName['error'])) {
        return ['error' => ['request_body' => $product, 'response_body' => $responseCategoryName['error']]];
      }

      $product['categoryName'] = $responseCategoryName['categoryName'] ?? '';
    }

    // Product with variations
    if ($product['format'] == 'V' and isset($response['variacoes'][0]['id'])) {
      foreach ($response['variacoes'] as $value):
        $product['skus'][] = [
          'id' => $value['id'] ?? '',
          'code' => $value['codigo'] ?? '',
          'salePrice' => $value['preco'] ?? '',
          'costPrice' => $value['precoCusto'] ?? '',
          'stock' => $value['estoque']['saldoVirtualTotal'] ?? 0,
          'stockLocation' => $value['estoque']['localizacao'] ?? '',
          'status' => $value['situacao'] ?? '',
          'weight' => $value['pesoLiquido'] ?? 0.0,
          'ncm' => $value['tributacao']['ncm'] ?? '',
          'gtin' => $value['gtin'] ?? '',
          'variations' => $this->parseVariations($value),
        ];
      endforeach;
    }

    if ($product['format'] == 'S') {
      $product['skus'] = [
        [
          'id' => $product['id'],
          'code' => $product['codigo'],
          'salePrice' => $product['price'],
          'costPrice' => 0,
          'stock' => $product['stock'],
          'stockLocation' => $product['stockLocation'],
          'status' => $product['status'],
          'weight' => $product['weight'],
          'ncm' => $product['ncm'],
          'gtin' => $product['gtin'],
          'variations' => [],
        ]
      ];
    }

    $braavoComponent = new BraavoProductComponent();
    $responsePlatform = $braavoComponent->sync($product);

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

  private function getCategoryName(int $id): array
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

    if (! isset($response['data']['descricao']) or $response['data']['descricao'] == 'Categoria padrão') {
      return [];
    }

    return ['categoryName' => $response['data']['descricao'] ];
  }
}