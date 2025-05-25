<?php

namespace App\Helpers;

class ConversionHelper
{
  public static function formatterJson(?string $json): string
  {
    if (empty($json)) {
      return '';
    }

    $array = json_decode($json);

    return json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  }

  public static function arrayToJson(array $array): string
  {
    if (empty($array)) {
      return '';
    }

    return json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }

  public static function jsonToArray(?string $json): array
  {
    if (empty($json)) {
      return [];
    }

    return json_decode($json, true);
  }
}
