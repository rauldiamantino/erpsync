<?php

namespace App\Helpers;

use App\Classes\Flash;

class RedirectHelper
{
  public static function to(string $url, ?string $type = null, ?string $message = null): void
  {
    if ($type and $message) {
      Flash::set($type, $message);
    }

    header('Location: ' . $url);
    exit;
  }
}
