<?php

namespace app\models;

use app\core\DBModel;

class VisitOffer extends DBModel
{
    public const NO_GUIDE = 0;
    public const GUIDE = 1;
    public int $offer_id = 0;
    public float $duration = 0.0;
    public int $guide = this::NO_GUIDE;
    public int $schedule_id = 0;


    public static function tableName(): string
    {
        // TODO: Implement tableName() method.
        return 'visit_offer';
    }

    public function attributes(): array
    {
        // TODO: Implement attributes() method.
        return ['offer_id', 'duration', 'guide', 'schedule_id'];
    }

    public static function pk(): string
    {
        // TODO: Implement pk() method.
        return 'offer_id';
    }

    public function rules(): array
    {
        // TODO: Implement rules() method.
        return [
            'offer_id' => [self::RULE_REQUIRED],
            'duration' => [self::RULE_REQUIRED],
            'guide' => [self::RULE_REQUIRED],
            'schedule_id' => [self::RULE_REQUIRED]
        ];
    }
}