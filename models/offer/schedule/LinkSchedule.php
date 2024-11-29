<?php

namespace app\models\offer\schedule;

use app\core\DBModel;


class LinkSchedule extends DBModel
{
    public int $id = 0;
    public int $schedule_id = 0;
    public int $offer_id = 0;

    public static function tableName(): string
    {
        return 'link_schedule';
    }

    public function attributes(): array
    {
        return ['schedule_id', 'offer_id'];
    }

    public static function pk(): string
    {
        return 'id';
    }

    public function rules(): array
    {
        return [
            'schedule_id' => [self::RULE_REQUIRED],
            'offer_id' => [self::RULE_REQUIRED]
        ];
    }
}