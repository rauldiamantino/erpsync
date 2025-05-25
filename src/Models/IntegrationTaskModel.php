<?php

namespace App\Models;

use App\Helpers\ConversionHelper;
use App\Models\Model;

class IntegrationTaskModel extends Model
{
  public function __construct()
  {
    parent::__construct();

    $this->setTable('integration_tasks');
  }

  public function scheduleTask(int $type, int $service, int $referenceId, array $dataTask = []): void
  {
    $data = [
      'type' => (int) $type,
      'service' => (int) $service,
      'reference_id' => (int) $referenceId,
      'attempts' => 0,
    ];

    if ($dataTask) {
      $data['data'] = ConversionHelper::arrayToJson($dataTask);
    }

    $this->createOrUpdate($data);
  }

  public function findNextTask(int $referenceType = 0): mixed
  {
    $typeWhere = '';

    if ($referenceType) {
      $typeWhere = '`type` = :type AND';
    }

    $sql = <<<SQL
  SELECT * FROM {$this->table} WHERE {$typeWhere} `attempts` < :attempts ORDER BY `id` ASC LIMIT :limit
  SQL;

    $data = [
      ':type' => $referenceType,
      ':attempts' => 3,
      ':limit' => 1,
    ];

    if (empty($referenceType)) {
      unset($data[':type']);
    }

    $result = $this->executeQuery($sql, $data);

    if (is_array($result)) {
      $result = $result[0];
    }

    return $result;
  }
}
