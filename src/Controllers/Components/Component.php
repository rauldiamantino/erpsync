<?php

namespace App\Controllers\Components;

class Component
{
  protected function returnError($responseBody, $requestBody = []): array
  {
    if (isset($responseBody['error'])) {
      $responseBody = $responseBody['error'];
    }

    return [
      'error' => [
        'response_body' => $responseBody,
        'request_body' => $requestBody,
      ],
    ];
  }

  protected function returnSuccess($responseBody, $requestBody = []): array
  {
    if (isset($responseBody['success'])) {
      $responseBody = $responseBody['success'];
    }

    return [
      'success' => [
        'response_body' => $responseBody,
        'request_body' => $requestBody,
      ],
    ];
  }
}
