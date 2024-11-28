<?php

namespace app\models\offer;
use app\core\DBModel;
use app\models\offer\schedule\ActivitySchedule;
use app\models\offer\schedule\LinkSchedule;

class ActivityOffer extends DBModel
{
    public int $offer_id = 0;
    public float $duration = 0.0;
    public int $required_age = 0;

    public static function tableName(): string
    {
        return 'activity_offer';
    }

    public function attributes(): array
    {
        return ['offer_id', 'duration', 'required_age'];
    }

    public static function pk(): string
    {
        return 'offer_id';
    }

    public function rules(): array
    {
        return [
            'duration' => [self::RULE_REQUIRED],
            'required_age' => [self::RULE_REQUIRED],
            'price' => [self::RULE_REQUIRED]
        ];
    }

    public function labels(): array
    {
        return [
            'duration' => "Durée de l'activité (h)",
            'required_age' => "Age minimum pour l'activité"
        ];
    }

    public function addSchedule($scheduleId)
    {
        $activitySchedule = new LinkSchedule();
        $activitySchedule->offer_id = $this->offer_id;
        $activitySchedule->schedule_id = $scheduleId;
        $activitySchedule->save();
    }
}