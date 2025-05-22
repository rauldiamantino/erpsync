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

  public function findNextTask(int $referenceType): mixed
  {
    $sql = <<<SQL
  SELECT * FROM {$this->table} WHERE `type` = :type AND `attempts` < :attempts ORDER BY `id` ASC LIMIT :limit
  SQL;

    $data = [
      ':type' => $referenceType,
      ':attempts' => 3,
      ':limit' => 1,
    ];

    $result = $this->executeQuery($sql, $data);

    if (is_array($result)) {
      $result = $result[0];
    }

    return $result;
  }
}
