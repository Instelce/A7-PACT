<?php

namespace app\models;

use app\core\DBModel;

class OfferOption extends DBModel
{
    public int $id = 0;
    public string $launch_date = '';
    public int $week_counter = 0;
    public int $duration = 0;
    public int $offer_id = 0;
    public static function tableName(): string
    {
        // TODO: Implement tableName() method.
        return 'offer_option';
    }

    public function attributes(): array
    {
        // TODO: Implement attributes() method.
        return ['launch_date', 'week_counter', 'duration', 'offer_id'];
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
            'launch_date' => [self::RULE_REQUIRED],
            'week_counter' => [self::RULE_REQUIRED],
            'duration' => [self::RULE_REQUIRED],
            'offer_id' => [self::RULE_REQUIRED],
        ];
    }
}