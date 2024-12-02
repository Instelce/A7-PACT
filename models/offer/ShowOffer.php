<?php

namespace app\models\offer;
use app\core\DBModel;

class ShowOffer extends DBModel
{
    public int $offer_id = 0;
    public int $capacity = 0;
    public float $duration = 0;

    public static function tableName(): string
    {
        return 'show_offer';
    }

    public function attributes(): array
    {
        return ['offer_id', 'capacity', 'duration'];
    }

    public static function pk(): string
    {
        return 'offer_id';
    }

    public function rules(): array
    {
        return [
            'capacity' => [self::RULE_REQUIRED],
            'duration' => [self::RULE_REQUIRED],
        ];
    }

    public function labels(): array
    {
        return [
            'duration' => "Durée du spectacle (h)",
            'capacity' => "Capacité d'accueil spectacle"
        ];
    }


}
