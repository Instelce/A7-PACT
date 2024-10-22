<?php

namespace app\models\offer;

use app\core\DBModel;

class VisitOffer extends DBModel
{
    public const NO_GUIDE = 0;
    public const GUIDE = 1;
    public int $offer_id = 0;
    public float $duration = 0.0;
    public int $guide = self::NO_GUIDE;
    public int $period_id = 0;

    public static function tableName(): string
    {
        return 'visit_offer';
    }

    public function attributes(): array
    {
        return ['offer_id', 'duration', 'guide', 'period_id'];
    }

    public static function pk(): string
    {
        return 'offer_id';
    }

    public function rules(): array
    {
        return [
            'offer_id' => [self::RULE_REQUIRED],
            'duration' => [self::RULE_REQUIRED],
            'guide' => [self::RULE_REQUIRED],
            'schedule_id' => [self::RULE_REQUIRED]
        ];
    }
}