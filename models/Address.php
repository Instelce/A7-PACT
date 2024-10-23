<?php

namespace app\models;

use app\core\DBModel;

class Address extends DBModel
{
    public int $id = 0;
    public int $number = 0;
    public string $street = '';
    public string $city = '';
    public string $postal_code = '';
    public float $longitude = 0.0;
    public float $latitude = 0.0;

    public static function tableName(): string
    {
        return 'address';
    }

    public function attributes(): array
    {
        return ['number', 'street', 'city', 'postal_code', 'longitude', 'latitude'];
    }

    public function rules(): array
    {
        return [
            'street' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
            'city' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
            'postal_code' => [self::RULE_REQUIRED]
        ];
    }
}