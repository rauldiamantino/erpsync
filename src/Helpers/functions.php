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
