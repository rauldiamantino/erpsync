<?php

namespace App\Helpers;

class ConversionHelper
{
  public static function formatterJson($json)
  {
    if (empty($json)) {
      return '';
    }

    $array = json_decode($json);

    return json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  }
}
