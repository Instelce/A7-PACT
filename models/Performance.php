<?php

namespace app\models;
use app\core\DBModel;
class Performance extends DBModel {
    public int $performance_id = 0;
    public string $name ='';
    public static function tableName(): string
    {
        return 'performance';
    }

    public function attributes(): array
    {
        return ['name'];
    }

    public static function pk(): string
    {
        return 'performance_id';
    }

    public function rules(): array
    {
        return [
            'name' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 128]]
        ];
    }
}