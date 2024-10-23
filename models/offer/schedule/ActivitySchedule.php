<?php

namespace app\models\offer\schedule;

use app\core\DBModel;

class ActivitySchedule extends DBModel
{
    public int $id = 0;
    public int $activity_id = 0;
    public int $schedule_id = 0;

    public static function tableName(): string
    {
        return 'activity_schedule';
    }

    public function attributes(): array
    {
        return ['activity_id', 'schedule_id'];
    }

    public function rules(): array
    {
        return [];
    }
}