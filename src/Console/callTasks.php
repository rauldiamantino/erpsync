<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Console\RouteCaller;

if ($argc < 2) {
  // php src/Console/callTasks.php controller/method 3
  echo "Usage: php callTasks.php <route_url> [interval_in_seconds]\n";

  exit(1);
}

$route = $argv[1];
$interval = isset($argv[2]) ? (int) $argv[2] : 3;

$caller = new RouteCaller($route, $interval);
$caller->run();