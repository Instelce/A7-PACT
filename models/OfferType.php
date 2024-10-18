<?php

namespace app\models;

use app\core\DBModel;

class OfferType extends DBModel
{
    public int $id = 0;
    public string $type = '';
    public float $price = 0.0;
    public int $offer_id = 0;


    public static function tableName(): string
    {
        // TODO: Implement tableName() method.
        return 'offer_type';

    }

    public function attributes(): array
    {
        // TODO: Implement attributes() method.
        return ['type', 'price', 'offer_id'];
    }

    public static function pk(): string
    {
        // TODO: Implement pk() method.
        return 'id';
    }

    public function rules(): array
    {
        // TODO: Implement rules() method.
        return [
            'type' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
            'price' => [self::RULE_REQUIRED],
            'offer_id' => [self::RULE_REQUIRED]
        ];
    }
}