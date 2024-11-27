<?php

namespace app\models\offer;

use app\core\DBModel;

class OfferType extends DBModel
{
    public const STANDARD_PRICE = 1.67;
    public const PREMIUM_PRICE = 3.33;

    public int $id = 0;
    public string $type = '';
    public float $price = 0.0;

    public static function tableName(): string
    {
        return 'offer_type';
    }

    public function attributes(): array
    {
        return ['type', 'price', 'offer_id'];
    }

    public function updateAttributes(): array
    {
        return [];
    }

    public function rules(): array
    {
        return [
            'type' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
            'price' => [self::RULE_REQUIRED],
            'offer_id' => [self::RULE_REQUIRED]
        ];
    }
}