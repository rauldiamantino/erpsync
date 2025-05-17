<?php

namespace App\Models;

use App\Models\Model;

class QueueModel extends Model
{
  public function __construct()
  {
    parent::__construct();

    $this->setTable('queue');
  }
}
