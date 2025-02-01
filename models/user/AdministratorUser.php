<?php

namespace app\models\user;
use app\core\DBModel;
use app\models\account\UserAccount;

class AdministratorUser extends DBModel
{
  public int $user_id = 0;
  public string $pseudo = '';

  public static function tableName(): string
  {
    return 'administrator_user';
  }

  public function attributes(): array
  {
    return ['user_id', 'pseudo'];
  }

  public static function pk(): string
  {
    return 'user_id';
  }

  public function rules(): array
  {
    return [];
  }

  public function account(): UserAccount
  {
    return UserAccount::findOne(['user_id' => $this->user_id]);
  }
}

