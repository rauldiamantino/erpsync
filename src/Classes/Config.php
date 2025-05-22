<?php

namespace App\Classes;

use App\Models\SettingModel;

class Config
{
  protected static ?SettingModel $settingModel = null;

  protected static function getModel(): SettingModel
  {
    if (self::$settingModel === null) {
      self::$settingModel = new SettingModel();
    }

    return self::$settingModel;
  }

  public static function get(string $name): ?string
  {
    return self::getModel()->findByName($name);
  }

  public static function set(string $name, string $value): void
  {
    self::getModel()->createOrUpdate(['name' => $name, 'value' => $value]);
  }
}
