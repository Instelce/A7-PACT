<?php

namespace app\models\offer\schedule;

use app\core\DBModel;

class AttractionParkSchedule extends DBModel
{
    public int $id = 0;
    public int $attraction_park_id = 0;
    public int $schedule_id = 0;

    public static function tableName(): string
    {
        return 'park_schedule';
    }

    public function attributes(): array
    {
        return ['attraction_park_id', 'schedule_id'];
    }

    public function rules(): array
    {
        return [];
    }
}