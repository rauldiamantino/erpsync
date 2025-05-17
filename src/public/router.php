<?php
$file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (php_sapi_name() === 'cli-server' && file_exists($file)) {
  return false;
}

require_once __DIR__ . '/index.php';