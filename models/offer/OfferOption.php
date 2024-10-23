<?php

namespace app\models\offer;

use app\core\DBModel;

class OfferOption extends DBModel
{
    public const EN_RELIEF = 'en_relief';
    public const A_LA_UNE = 'a_la_une';

    public int $id = 0;
    public string $type = '';
    public string $launch_date = '';
    public int $duration = 0;
    public int $offer_id = 0;

    public static function tableName(): string
    {
        return 'offer_option';
    }

    public function attributes(): array
    {
        return ['type', 'launch_date', 'duration', 'offer_id'];
    }

    public function rules(): array
    {
        return [
            'launch_date' => [self::RULE_REQUIRED],
            'duration' => [self::RULE_REQUIRED],
            'offer_id' => [self::RULE_REQUIRED],
        ];
    }

    /**
     * Calculate the time when the offer will be deactivated
     * and check if the offer is still active
     *
     * @return boolean
     */
    public function isOfferActive(): bool
    {
        $launchDate = strtotime($this->launch_date);
        $duration = $this->duration;
        $endDate = strtotime("+$duration days", $launchDate);
        $today = strtotime(date('Y-m-d'));

        if ($today > $endDate) {
            return false;
        }

        return true;
    }

    /**
     * Get the price of the offer
     *
     * @return float
     */
    public function getPrice(): float
    {
        if ($this->type === self::EN_RELIEF) {
            return 2.98;
        } else if ($this->type === self::A_LA_UNE) {
            return 7.98;
        }
        return 0;
    }
}