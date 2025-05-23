<?php

namespace App\Helpers;

use App\Classes\Constants\ServiceType;
use App\Classes\Constants\ReferenceType;

class TypeHelper
{
  public static function getServiceName(?int $serviceType): string
  {
    $services = [
      ServiceType::BLING => 'Bling',
      ServiceType::BRAAVO => 'Braavo',
    ];

    return $services[ $serviceType ] ?? 'Desconhecido';
  }

  public static function getReferenceName(?int $referenceType): string
  {
    $referencesTypes = [
      ReferenceType::PRODUCT => 'Produto',
      ReferenceType::SKU => 'Sku',
      ReferenceType::CATEGORY => 'Categoria',
      ReferenceType::BRAND => 'Marca',
      ReferenceType::STOCK => 'Estoque',
      ReferenceType::SUPPLIER => 'Fornecedor',
    ];

    return $referencesTypes[ $referenceType ] ?? 'Desconhecido';
  }
}
