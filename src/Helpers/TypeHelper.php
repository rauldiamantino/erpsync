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

  public static function getReferenceName(?int $referenceType, bool $plural = false): string
  {
    $referencesTypes = [
      ReferenceType::PRODUCT => 'Product',
      ReferenceType::SKU => 'Sku',
      ReferenceType::CATEGORY => 'Category',
      ReferenceType::BRAND => 'Brand',
      ReferenceType::STOCK => 'Stock',
      ReferenceType::SUPPLIER => 'Supplier',
    ];

    if ($plural) {
      $referencesTypes = [
        ReferenceType::PRODUCT => 'Products',
        ReferenceType::SKU => 'Skus',
        ReferenceType::CATEGORY => 'Categories',
        ReferenceType::BRAND => 'Brands',
        ReferenceType::STOCK => 'Stocks',
        ReferenceType::SUPPLIER => 'Suppliers',
      ];
    }

    return $referencesTypes[ $referenceType ] ?? 'Unknown';
  }
}
