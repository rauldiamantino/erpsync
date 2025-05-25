<?php

namespace App\Helpers;

use App\Classes\Flash;
use App\Helpers\ConversionHelper;

class RedirectHelper
{
  public static function to(string $url, string $type, ?string $message = null)
  {
    if (isset($_SERVER['HTTP_X_REQUESTED_BY']) and $_SERVER['HTTP_X_REQUESTED_BY'] === 'CLI-Script') {
      echo ConversionHelper::arrayToJson([$type => $message]);
      exit;
    }

    if ($type and $message) {
      Flash::set($type, $message);
    }

    header('Location: ' . $url);
    exit;
  }
}
