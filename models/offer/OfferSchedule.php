<?php
namespace app\models\offer;
use app\core\DBModel;


class OfferSchedule extends DBModel
{
  public int $id = 0;
  public int $offer_id = 0;
  public int $day = 0;
  public string $opening_hours = '';
  public string $closing_hours = '';

  public static function tableName(): string
  {
    return 'offer_schedule';
  }

  public function attributes(): array
  {
    return ['offer_id', 'day', 'opening_hours', 'closing_hours'];
  }

  public static function pk(): string
  {
    return 'id';
  }

  public function rules(): array
  {
    return [
      'offer_id' => [self::RULE_REQUIRED],
      'day' => [self::RULE_REQUIRED],
      'opening_hours' => [self::RULE_REQUIRED],
      'closing_hours' => [self::RULE_REQUIRED]
    ];
  }
}

?>