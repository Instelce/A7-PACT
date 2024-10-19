<?php

namespace app\models\offer;
use app\core\DBModel;

class OfferPeriod extends DBModel {
    public int $period_id = 0;
    public string $start_date ='';
    public string $end_date = '';

    public static function tableName(): string
    {
        return 'offer_period';
    }

    public function attributes(): array
    {
        return ['start_date', 'end_date'];
    }

    public static function pk(): string
    {
        return 'period_id';
    }

    public function rules(): array
    {
        return [
            'start_date' => [self::RULE_REQUIRED, self::RULE_DATE],
            'end_date' => [self::RULE_REQUIRED, self::RULE_DATE]
        ];
    }
}