<?php

namespace App\Classes;

class Flash
{
  public static function set(string $type, string $message): void
  {
    $_SESSION['flash'][ $type ] = $message;
  }

  public static function get(string $type): ?string
  {
    if (! isset($_SESSION['flash'][ $type ])) {
      return null;
    }

    $message = $_SESSION['flash'][ $type ];
    unset($_SESSION['flash'][ $type ]);

    return htmlspecialchars($message);
  }

  public static function has(string $type): bool
  {
    return isset($_SESSION['flash'][ $type ]);
  }
}
