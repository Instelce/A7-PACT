<?php

namespace app\models;
use app\core\DBModel;

class AdministratorUser extends DBModel
{
  public int $admin_id = 0;

  public static function tableName(): string
  {
    return 'administrator_user';
  }

  public function attributes(): array
  {
    return ['admin_id'];
  }

  public static function pk(): string
  {
    return 'admin_id';
  }


  public function rules(): array
  {
    return [];
  }
}

