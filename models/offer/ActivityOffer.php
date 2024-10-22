<?php

namespace app\models\offer;
use app\core\DBModel;

class ActivityOffer extends DBModel {
    public int $offer_id = 0;
    public float $duration =0.0;
    public int $required_age = 0;
    public float $price = 0.0;

    public static function tableName(): string
    {
        return 'activity_offer';
    }

    public function attributes(): array
    {
        return ['duration', 'required_age', 'price'];
    }

    public static function pk(): string
    {
        return 'offer_id';
    }

    public function rules(): array
    {
        return [
            'duration' => [self::RULE_REQUIRED],
            'required_age' => [self::RULE_REQUIRED],
            'price' => [self::RULE_REQUIRED]
        ];
    }
}