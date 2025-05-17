<?php

namespace App\Models;

use App\Models\Model;

class TransmissionModel extends Model
{
  public function __construct()
  {
    parent::__construct();

    $this->setTable('transmissions');
  }
}
