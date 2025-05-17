<?php

use App\Models\SettingModel;

function getSetting(string $name): string
{
  static $instance = null;

  if ($instance === null) {
    $instance = new SettingModel();
  }

  return $instance->findByName($name);
}

function setSetting(string $name, $value): void
{
  static $instance = null;

  if ($instance === null) {
    $instance = new SettingModel();
  }

  $data = [
    'name' => $name,
    'value' => $value,
  ];

  $instance->createOrUpdate($data);
}

function pr($data, $type = false) {
  echo '<pre>';

  if ($type) {
    var_dump(print_r($data));
  }
  else {
    print_r($data);
  }

  echo '</pre>';
}
