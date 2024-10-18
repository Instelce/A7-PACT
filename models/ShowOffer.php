<?php

namespace app\models;
use app\core\DBModel;
class ShowOffer extends DBModel {
    public int $offer_id = 0;
    public int $capacity = 0;
    public float $duration = 0.0;
    public int $schedule_id = 0;
    public static function tableName(): string
    {
        return 'show_offer';
    }

    public function attributes(): array
    {
        return ['capacity', '$duration', '$schedule_id'];
    }

    public static function pk(): string
    {
        return 'offer_id';
    }

    public function rules(): array
    {
        return [
            'capacity' => [self::RULE_REQUIRED],
            '$duration' => [self::RULE_REQUIRED],
            '$schedule_id' => [self::RULE_REQUIRED]
        ];
    }
}