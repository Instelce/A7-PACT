<?php

namespace app\models\user;
use app\core\DBModel;
use app\models\account\UserAccount;

class AdministratorUser extends DBModel
{
  public int $user_id = 0;

  public static function tableName(): string
  {
    return 'administrator_user';
  }

  public function attributes(): array
  {
    return [];
  }

  public static function pk(): string
  {
    return 'user_id';
  }

  public function rules(): array
  {
    return [];
  }

  public function user(): UserAccount {
      return UserAccount::findOne(['account_id' => $this->user_id]);
  }
}

