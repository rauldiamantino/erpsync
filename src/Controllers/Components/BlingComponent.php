<?php

namespace App\Controllers\Components;

use App\Classes\Config;
use App\Classes\CurlRequest;
use App\Controllers\Components\Component;

class BlingComponent extends Component
{
  private string $baseUrl;
  private string $clientId;
  private string $clientSecret;
  private string $accessToken;
  private string $refreshToken;
  private string $expiresToken;

  public function __construct()
  {
    $this->baseUrl = 'https://api.bling.com.br/Api/v3';
    $this->clientId = Config::get('bling_client_id');
    $this->clientSecret = Config::get('bling_client_secret');
    $this->accessToken = Config::get('bling_access_token');
    $this->refreshToken = Config::get('bling_refresh_token');
    $this->expiresToken = Config::get('bling_expires_in');
  }

  public function sendRequest($method, $endpoint, $headers, $body = null, $queryParams = null)
  {
    $renewToken = $this->renewToken();

    if (isset($renewToken['error'])) {
      return $renewToken;
    }

    $method = strtolower($method);
    $url = $this->baseUrl . $endpoint;

    // Add bearer token to authorize the API request
    $headers['Authorization'] = 'Bearer ' . $this->accessToken;

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

  public function getTokenByExchangeCode(string $code): array
  {
    if (empty($code)) {
      return ['error' => 'Authorization code is missing.'];
    }

    $url = $this->baseUrl . '/oauth/token';

    $headers = [
      'Content-Type' => 'application/x-www-form-urlencoded',
      'Accept' => '1.0',
      'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
    ];

    $body = [
      'grant_type' => 'authorization_code',
      'code' => $code,
    ];

    $response = CurlRequest::post($url, $headers, $body);

    if (! is_array($response) or empty($response)) {
      return ['error' => 'Failed to get token by exchange code: no valid response received from the Bling API.'];
    }

    if (isset($response['error']) and $response['error']) {
      return $response;
    }

    return $this->persistTokens($response);
  }

  private function renewToken(): array
  {
    if (empty($this->refreshToken)) {
      return ['error' => 'Invalid refresh token.'];
    }

    if ($this->expiresToken > time() + 60) {
      return ['success' => true, 'message' => 'Token is still valid.'];
    }

    $url = $this->baseUrl . '/oauth/token';

    $headers = [
      'Content-Type' => 'application/x-www-form-urlencoded',
      'Accept' => '1.0',
      'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
    ];

    $body = [
      'grant_type' => 'refresh_token',
      'refresh_token' => $this->refreshToken,
    ];

    $response = CurlRequest::post($url, $headers, $body);

    if (! is_array($response) or empty($response)) {
      return ['error' => 'Failed to refresh token: no valid response received from the Bling API.'];
    }

    if (isset($response['error']) and $response['error']) {
      return $response;
    }

    return $this->persistTokens($response);
  }

  private function persistTokens(array $response): array
  {
    $missingFields = [];

    if (! isset($response['access_token']) or empty($response['access_token'])) {
      $missingFields[] = 'access_token';
    }

    if (! isset($response['expires_in']) or empty($response['expires_in'])) {
      $missingFields[] = 'expires_in';
    }

    if (! isset($response['refresh_token']) or empty($response['refresh_token'])) {
      $missingFields[] = 'refresh_token';
    }

    if ($missingFields) {
      return ['error' => 'Incomplete response from Bling API. Missing fields: ' . implode(', ', $missingFields)];
    }

    // Set tokens for immediate use
    $this->accessToken = $response['access_token'];
    $this->refreshToken = $response['refresh_token'];
    $this->expiresToken = $response['expires_in'] + time();

    Config::set('bling_access_token', $this->accessToken);
    Config::set('bling_refresh_token', $this->refreshToken);
    Config::set('bling_expires_in', $this->expiresToken);

    return ['success' => true, 'message' => ''];
  }
}