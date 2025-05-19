<?php

namespace App\Models;

use App\Models\Model;

class IntegrationLogModel extends Model
{
  public function __construct()
  {
    parent::__construct();

    $this->setTable('integration_logs');
  }
}
