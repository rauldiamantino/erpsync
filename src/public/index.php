<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../Helpers/functions.php';

use App\Classes\CurlRequest;

$headers = [
  'Content-Type' => 'application/json',
  'Authorization' => 'Bearer abc1234',
];

$response = CurlRequest::get('https://jsonplaceholder.typicode.com/posts/10', $headers);

pr($response);