<?php

namespace App\Console;

use App\Classes\CurlRequest;
use App\Helpers\ConversionHelper;
use Exception;

class RouteCaller
{
  private int $interval;
  private string $url;

  public function __construct(string $route, int $interval = 3)
  {
    $this->url = 'http://localhost:8080/' . $route;
    $this->interval = $interval;
  }

  public function run(): void
  {
    echo "Calling: " . ($this->url ?? 'no route passed') . "\n";
    echo "Interval between calls: {$this->interval}s\n";

    while (true) {
      $start = microtime(true);

      try {
        $headers = ['X-Requested-By' => 'CLI-Script'];
        $response = CurlRequest::get($this->url, $headers, null, null);

        $time = round(microtime(true) - $start, 2);

        echo ConversionHelper::arrayToJson($response) . " - [$time s] \n";
      }
      catch (Exception $e) {
        echo "Exception: " . $e->getMessage() . "\n";
      }

      if ($this->interval > 0) {
        sleep($this->interval);
      }
    }
  }
}