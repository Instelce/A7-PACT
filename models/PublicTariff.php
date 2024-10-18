<?php

namespace app\models;

use app\core\DBModel;

class PublicTariff extends DBModel
{
    public int $id = 0;
    public string $denomination = '';
    public float $price = 0.0;
    public int $offer_id = 0;
    public static function tableName(): string
    {
        // TODO: Implement tableName() method.
        return 'public_tariff';
    }

    public function attributes(): array
    {
        // TODO: Implement attributes() method.
        return ['denomination', 'price', 'offer_id'];
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
            'denomination' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
            'price' => [self::RULE_REQUIRED],
            'offer_id' => [self::RULE_REQUIRED]
        ];
    }
}