<?php

namespace App\Classes;

class CurlRequest
{
  public static function post(string $url, array $headers = [], $body = null): array
  {
    return self::request('POST', $url, $headers, $body);
  }

  public static function put(string $url, array $headers = [], $body = null): array
  {
    return self::request('PUT', $url, $headers, $body);
  }

  public static function patch(string $url, array $headers = [], $body = null): array
  {
    return self::request('PATCH', $url, $headers, $body);
  }

  public static function delete(string $url, array $headers = []): array
  {
    return self::request('DELETE', $url, $headers);
  }

  public static function getType(): string
  {
    if (isset($_SERVER['HTTP_X_REQUESTED_BY']) and $_SERVER['HTTP_X_REQUESTED_BY'] === 'CLI-Script') {
      return 'script';
    }

    return '';
  }

  private static function request(string $method, string $url, array $headers = [], $body = null, bool $isJson = true)
  {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

    if ($body !== null) {

      // Body
      $hasContentType = false;
      $contentType = 'application/json';

      foreach ($headers as $key => $value):

        if (strtolower($key) === 'content-type') {
          $hasContentType = true;
          $contentType = strtolower($value);
          break;
        }
      endforeach;

      if (! $hasContentType) {
        $headers['Content-Type'] = 'application/json';
        $contentType = 'application/json';
      }

      if (is_array($body)) {

        if ($contentType === 'application/json') {
          $body = json_encode($body);
        } elseif ($contentType === 'application/x-www-form-urlencoded') {
          $body = http_build_query($body);
        }
      }

      curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }

    // Header
    if ($headers) {
      $formattedHeaders = [];
      foreach ($headers as $key => $value):
        $formattedHeaders[] = $key . ': ' . $value;
      endforeach;

      curl_setopt($ch, CURLOPT_HTTPHEADER, $formattedHeaders);
    }

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    // Try to decode JSON response
    $decoded = json_decode($response, true);

    if (json_last_error() === JSON_ERROR_NONE and is_array($decoded)) {
      $response = $decoded;
    } else {
      $response = $response;
    }

    self::logRequest([
      'method' => $method,
      'url' => $url,
      'headers' => $headers,
      'body' => $body,
      'response' => $response,
      'code' => $httpCode,
      'error' => $error,
    ]);

    return $response;
  }

  public static function get(string $url, array $headers = [], array $queryParams = null, $body = null): mixed
  {
    if ($queryParams) {
      $queryString = http_build_query($queryParams);

      if (strpos($url, '?') === false) {
        $url .= '?';
      }
      else {
        $url .= '&';
      }

      $url .= $queryString;
    }

    return self::request('GET', $url, $headers, $body);
  }

  private static function logRequest(array $data): void
  {
    $logDir = __DIR__ . '/../temp/logs/';
    $logFile = $logDir . 'curl-' . date('Y-m-d') . '.log';

    if (!is_dir($logDir)) {
      mkdir($logDir, 0777, true);
    }

    $curlCmd = "curl -X " . strtoupper($data['method']);

    // Add headers
    if ($data['headers']) {
      foreach ($data['headers'] as $key => $value):
        $curlCmd .= " \\\n  -H '" . $key . ": " . $value . "'";
      endforeach;
    }

    // Add body if exists and method supports it
    if ($data['body']) {
      $body = $data['body'];

      if (is_array($data['body'])) {
        $body = json_encode($data['body'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
      }

      // Escape single quotes inside body
      $bodyEscaped = str_replace("'", "'\\''", $body);

      $curlCmd .= " \\\n  --data '" . $bodyEscaped . "'";
    }

    // Add URL
    $curlCmd .= " \\\n  '" . $data['url'] . "'";

    // Format response
    $responseText = is_array($data['response']) ? json_encode($data['response'], JSON_UNESCAPED_UNICODE) : $data['response'];

    // Log entry
    $logEntry = date('Y-m-d H:i:s') . " ------------------------------------------------------------------------------------------------------------\n\n";
    $logEntry .= $curlCmd . "\n\n";
    $logEntry .= "Response: " . $data['code'] . "\n\n" . $responseText . "\n\n";

    if (isset($data['error']) and $data['error']) {
      $logEntry .= "Error: " . $data['error'] . "\n\n";
    }

    file_put_contents($logFile, $logEntry, FILE_APPEND);
  }
}