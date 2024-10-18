<?php
namespace app\models;
use app\core\DBModel;

class ProfessionalUser extends DBModel
{

  public int $pro_id = 0;
  public int $code = 0;
  public string $denomination = '';
  public string $siren = '';

  public static function tableName(): string
  {
    return 'professional_user';
  }

  public function attributes(): array
  {
    return ['pro_id', 'code', 'denomination', 'siren'];
  }

  public static function pk(): string
  {
    return 'pro_id';
  }


  public function rules(): array
  {
    return [
      'code' => [self::RULE_REQUIRED],
      'denomination' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 100], [self::RULE_UNIQUE]],
      'siren' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 14], [self::RULE_UNIQUE]]
    ];
  }


}
