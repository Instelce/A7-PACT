<?php
namespace app\models\account;
use app\core\DBModel;

class AnonymousAccount extends DBModel
{
  public int $account_id = 0;
  public string $pseudo = '';

  public static function tableName(): string
  {
    return 'anonymous_account';
  }

  public function attributes(): array
  {
    return ['account_id', 'pseudo'];
  }

  public static function pk(): string
  {
    return 'account_id';
  }

  public function rules(): array
  {
    return [
      'pseudo' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 15]]
    ];
  }

  public function account(): Account
  {
    return Account::findOne(['id' => $this->account_id]);
  }
}