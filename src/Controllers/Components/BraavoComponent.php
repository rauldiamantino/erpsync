<?php

namespace App\Controllers\Components;

use App\Classes\CurlRequest;

class BraavoComponent
{
  private string $baseUrl;
  private string $apiToken;
  private string $apiKey;

  public function __construct()
  {
    $this->baseUrl = getSetting('braavo_store_url');
    $this->apiToken = getSetting('braavo_api_token');
    $this->apiKey = getSetting('braavo_api_key');
  }

  public function sendRequest($method, $endpoint, $headers, $body = null, $queryParams = null)
  {
    $method = strtolower($method);
    $url = $this->baseUrl . '/api' . $endpoint;

    // Add basic token to authorize the API request
    $headers['Authorization'] = 'Basic ' . base64_encode($this->apiToken . ':' . $this->apiKey);

    // Respect rate limit
    sleep(1);

    return match ($method) {
      'get' => CurlRequest::get($url, $headers, $queryParams, $body),
      'post' => CurlRequest::post($url, $headers, $body),
      'put' => CurlRequest::put($url, $headers, $body),
      'patch' => CurlRequest::patch($url, $headers, $body),
      'delete' => CurlRequest::delete($url, $headers),
      default => ['error' => 'Unsupported HTTP method: ' . strtoupper($method)],
    };
  }
}