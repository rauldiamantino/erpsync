<?php

namespace App\Helpers;

use App\Classes\Flash;
use App\Helpers\ConversionHelper;

class RedirectHelper
{
  public static function to(string $url, string $type = '', ?string $message = null)
  {
    if ($type and $message) {
      Flash::set($type, $message);
    }

    header('Location: ' . $url);
    exit;
  }
}
