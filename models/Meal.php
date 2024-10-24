<?php

namespace app\models;
use app\core\DBModel;
class Meal extends DBModel {
    public int $meal_id = 0;
    public string $name ='';
    public float  $price = 0;
    public static function tableName(): string
    {
        return 'meal';
    }

    public function attributes(): array
    {
        return ['name', 'price'];
    }

    public static function pk(): string
    {
        return 'meal_id';
    }

    public function rules(): array
    {
        return [
            'name' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 128]],
            'price' => [self::RULE_REQUIRED]
        ];
    }
}