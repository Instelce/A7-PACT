<?php

namespace app\models\account;
use app\core\DBModel;

class UserAccount extends DBModel
{
  public int $user_id = 0;
  public string $mail = '';
  public string $password = '';
  public string $avatarUrl = '';

  public static function tableName(): string
  {
    return 'user_account';
  }

  public function attributes(): array
  {
    return ['user_id', 'mail', 'password', 'avatarUrl'];
  }

  public static function pk(): string
  {
    return 'user_id';
  }


  public function rules(): array
  {
    return [
      'mail' => [self::RULE_REQUIRED, [self::RULE_MAIL], [self::RULE_MAX, 'max' => 100], [self::RULE_UNIQUE]],
      'password' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8], [self::RULE_MAX, 'max' => 100]],
      'avatarUrl' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]]
    ];
  }


}

