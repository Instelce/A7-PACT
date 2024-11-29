<?php

namespace app\models\offer;
use app\core\DBModel;
use app\models\offer\schedule\ActivitySchedule;
use app\models\offer\schedule\AllOfferSchedule;
use app\models\offer\schedule\AttractionParkSchedule;
use app\models\offer\schedule\LinkSchedule;

class AttractionParkOffer extends DBModel
{
    public int $offer_id = 0;
    public string $url_image_park_map = '';
    public int $attraction_number = 0;
    public int $required_age = 0;

    public static function tableName(): string
    {
        return 'attraction_park_offer';
    }

    public function attributes(): array
    {
        return ['offer_id', 'url_image_park_map', 'required_age'];
    }

    public static function pk(): string
    {
        return 'offer_id';
    }

    public function rules(): array
    {
        return [
            'url_image_park_map' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
            'required_age' => [self::RULE_REQUIRED],
            'attraction_number' => [self::RULE_REQUIRED]
        ];
    }

    public function addSchedule($scheduleId)
    {
        $parkSchedule = new LinkSchedule();
        $parkSchedule->offer_id = $this->offer_id;
        $parkSchedule->schedule_id = $scheduleId;
        $parkSchedule->save();
    }

    public function labels(): array
    {
        return [
            'url_image_park_map' => "Plan du parc",
            'required_age' => "Age minimum requis"
        ];
    }
}

