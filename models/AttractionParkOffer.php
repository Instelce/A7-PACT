<?php

namespace app\models;
use app\core\DBModel;
class AttractionParkOffer extends DBModel {
    public int $offer_id = 0;
    public string $url_image_park_map ='';
    public int $required_age = 0;
    public static function tableName(): string
    {
        return 'attraction_park_offer';
    }

    public function attributes(): array
    {
        return ['url_image_park_map', 'required_age'];
    }

    public static function pk(): string
    {
        return 'offer_id';
    }

    public function rules(): array
    {
        return [
            'url_image_park_map' => [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 255]],
            'required_age' => [self::RULE_REQUIRED]
        ];
    }
}