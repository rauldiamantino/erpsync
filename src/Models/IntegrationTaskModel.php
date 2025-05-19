<?php

namespace App\Models;

use App\Models\Model;

class IntegrationTaskModel extends Model
{
  public function __construct()
  {
    parent::__construct();

    $this->setTable('integration_tasks');
  }

  public function scheduleTask(int $type, int $service, int $reference_id): void
  {
    $data = [
      'type' => (int) $type,
      'service' => (int) $service,
      'reference_id' => (int) $reference_id,
      'attempts' => 0,
    ];

    $this->createOrUpdate($data);
  }
}
