<?php

namespace App\Controllers\Components;

use Dotenv\Dotenv;
use App\Classes\CurlRequest;

Class BlingComponent
{
  private string $baseUrl = 'https://api.bling.com.br/Api/v3';
  private string $clientId = getSetting('bling_client_id');
  private string $clientSecret = getSetting('bling_client_secret');

  public function getTokenByExchangeCode()
  {

  }

  public function renewToken()
  {

  }

  public function sendRequest()
  {

  }
}