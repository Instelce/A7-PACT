<?php

namespace app\models\user;
use app\core\DBModel;
use app\models\account\UserAccount;

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

  public function user(): UserAccount {
      return UserAccount::findOne(['account_id' => $this->admin_id]);
  }
}

