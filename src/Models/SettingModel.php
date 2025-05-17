<?php

namespace App\Models;

use App\Models\Model;

class SettingModel extends Model
{
  public function __construct()
  {
    parent::__construct();

    $this->setTable('settings');
  }

  public function findByName(string $name): string
  {
    $result = $this->findBy('name', $name);
    $value = $result['value'] ?? '';

    if (is_array($value)) {
      $value = '';
    }

    return $value;
  }
}