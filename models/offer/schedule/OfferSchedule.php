<?php

namespace app\models\offer\schedule;

use app\core\DBModel;


class OfferSchedule extends DBModel
{
    public int $id = 0;

    /**
     * Day of the week (1 = Monday, 2 = Tuesday, ..., 7 = Sunday)
     */
    public int $day = 0;

    public string $opening_hours = '';
    public string $closing_hours = '';

    public static function tableName(): string
    {
        return 'offer_schedule';
    }

    public function attributes(): array
    {
        return ['day', 'opening_hours', 'closing_hours'];
    }

    public static function pk(): string
    {
        return 'id';
    }

    public function rules(): array
    {
        return [
            'day' => [self::RULE_REQUIRED],
            'opening_hours' => [self::RULE_REQUIRED],
            'closing_hours' => [self::RULE_REQUIRED]
        ];
    }
}

?>