<?php

namespace app\models\offer;

use app\core\DBModel;

class VisitOffer extends DBModel
{
    public int $offer_id = 0;
    public float $duration = 0.0;
    public int $guide = 0;


    public static function tableName(): string
    {
        return 'visit_offer';
    }

    public function attributes(): array
    {
        return ['offer_id', 'duration', 'guide'];
    }

    public static function pk(): string
    {
        return 'offer_id';
    }

    public function rules(): array
    {
        return [
            'offer_id' => [self::RULE_REQUIRED],
            'duration' => [self::RULE_REQUIRED],
            'guide' => [self::RULE_REQUIRED],
        ];
    }

    public function labels(): array
    {
        return [
            'duration' => "Durée de la visite (h)",
            'guide' => 'Avec guide de visite',
            // 'period_id' => 'A une période'
            // "Langues disponibles pour la visite"
        ];
    }
}