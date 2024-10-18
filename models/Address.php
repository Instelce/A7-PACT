<?php

namespace app\models;

use app\core\DBModel;

class Address extends DBModel
{
    public int $id = 0;
    public int $number = 0;
    public string $name = '';
    public string $city = '';
    public int $postal_code = 0;
    public float $longitude = 0.0;
    public float $latitude = 0.0;


    public static function tableName(): string
    {
        // TODO: Implement tableName() method.
        return 'address';
    }

    public function attributes(): array
    {
        // TODO: Implement attributes() method.
        return ['number', 'name', 'city', 'postal_code', 'longitude', 'latitude'];
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
            'number' => [self::RULE_REQUIRED],
            'name' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
            'city' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
            'postal_code' => [self::RULE_REQUIRED]
        ];
    }
}