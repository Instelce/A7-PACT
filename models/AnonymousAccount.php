<?php
namespace app\models;
use app\core\DBModel;

class AnonymousAccount extends DBModel
{
  public int $anonymous_id = 0;
  public string $pseudo = '';

  public static function tableName(): string
  {
    return 'anonymous_account';
  }

  public function attributes(): array
  {
    return ['anonymous_id', 'pseudo'];
  }

  public static function pk(): string
  {
    return 'anonymous_id';
  }

  public function rules(): array
  {
    return [
      'pseudo' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 15]]
    ];
  }
}