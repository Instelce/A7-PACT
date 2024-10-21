<?php
namespace app\models\user\professional;
use app\core\DBModel;
use app\models\account\UserAccount;

class ProfessionalUser extends DBModel
{

  public int $user_id = 0;
  public int $code = 0;
  public string $denomination = '';
  public string $siren = '';

  public static function tableName(): string
  {
    return 'professional_user';
  }

  public function attributes(): array
  {
    return ['code', 'denomination', 'siren'];
  }

  public static function pk(): string
  {
    return 'user_id';
  }

  public function rules(): array
  {
    return [
      'code' => [self::RULE_REQUIRED],
      'denomination' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 100], [self::RULE_UNIQUE]],
      'siren' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 14], [self::RULE_UNIQUE]]
    ];
  }

  public function user(): UserAccount {
      return UserAccount::findOne(['account_id' => $this->user_id]);
  }
}
