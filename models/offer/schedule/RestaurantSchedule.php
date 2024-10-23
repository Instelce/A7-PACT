<?php

namespace app\models\offer\schedule;

use app\core\DBModel;

class RestaurantSchedule extends DBModel
{
    public int $id = 0;
    public int $restaurant_id = 0;
    public int $schedule_id = 0;

    public static function tableName(): string
    {
        return 'restaurant_schedule';
    }

    public function attributes(): array
    {
        return ['restaurant_id', 'schedule_id'];
    }

    public function rules(): array
    {
        return [];
    }
}