<?php

namespace app\models\offer;

use app\core\DBModel;
use DateInterval;
use DateTime;

class Subscription extends DBModel
{
    public const EN_RELIEF = 'en_relief';
    public const A_LA_UNE = 'a_la_une';

    public const EN_RELIEF_PRICE = 8.34;
    public const A_LA_UNE_PRICE = 16.68;

    public int $id = 0;
    public string $launch_date = '';
    public int $duration = 0;
    public int $offer_id = 0;
    public int $option_id = 0;

    public static function tableName(): string
    {
        return 'subscription';
    }

    public function attributes(): array
    {
        return ['launch_date', 'duration', 'offer_id', 'option_id'];
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

    public function endDate(): string
    {
        $date = new DateTime($this->launch_date);
        $interval = DateInterval::createFromDateString($this->duration * 7 . " days");
        $date->add($interval);
        return $date->format('Y-m-d');
    }

    public function durationInDays()
    {
        $launchDate = strtotime($this->launch_date);
        $today = date("Y-m-d");

        return ceil(abs(strtotime($today) - $launchDate) / 86400);
    }

    public function durationInWeek()
    {
        $launchDate = strtotime($this->launch_date);
        $today = date("Y-m-d");

        return ceil(abs(strtotime($today) - $launchDate) / 604800);
    }

    public function option(): Option
    {
        return Option::findOneByPk($this->option_id);
    }

    public function price(): float|int
    {
        return $this->option()->price;
    }

    public function type()
    {
        return $this->option()->type;
    }
}