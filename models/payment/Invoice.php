<?php

namespace app\models\payment;

/** @var $subscription  \app\models\offer\Subscription */

use app\core\DBModel;
use app\models\offer\Offer;
use app\models\offer\OfferStatusHistory;
use app\models\offer\OfferType;
use app\models\offer\Option;

class Invoice extends DBModel
{
    public int $id = 0;
    public string $issue_date = '';
    // Month number
    public string $service_date = '';
    public string $due_date = '';
    public float $offer_price = 0.0;
    public float $en_relief_price = 0.0;
    public float $a_la_une_price = 0.0;

    public int $offer_id = 0;

    public function save(): bool
    {
        $this->offer_price = OfferType::findOneByPk(Offer::findOneByPk($this->offer_id)->offer_type_id)->price;
        $this->a_la_une_price = Option::findOneByPk(2)->price;
        $this->en_relief_price = Option::findOneByPk(1)->price;
        return parent::save();
    }

    public function rules(): array
    {
        return [];
    }

    public static function tableName(): string
    {
        return 'invoice';
    }

    public function attributes(): array
    {
        return ['issue_date', 'service_date', 'due_date', 'offer_price', 'en_relief_price', 'a_la_une_price', 'offer_id'];
    }

    public function offer()
    {
        return Offer::findOne(['id' => $this->offer_id]);
    }

    /**
     * Count activate days in order of the history for the invoice month
     */
    public function activeDays(): int
    {
        $offer = $this->offer();
        $lastMonthHistories = OfferStatusHistory::query()->filters(['offer_id' => $offer->id])->search(['created_at' => date('Y-m', strtotime( $this->issue_date . "-1 month"))])->make();
        $histories = OfferStatusHistory::query()->filters(['offer_id' => $offer->id])->search(['created_at' => date('Y-m', strtotime($this->issue_date))])->make();
        $lastMonthDay = date('t', strtotime(date('Y-m', strtotime($this->issue_date))));
        $count = 0;

        // Set status
        if (empty($lastMonthHistories)) {
            $status = $offer->offline ? "offline" : "online";
        } else {
            $status = $lastMonthHistories[count($lastMonthHistories) - 1]->switch_to;
        }

//        echo "<pre>";
        for ($day = 0; $day <= $lastMonthDay; $day++) {
            // Check if the status has change on this day
            $dayHistories = array_filter($histories, fn($history) => date('d', strtotime($history->created_at)) == $day);
            $dayHistories = array_values($dayHistories);

            if (!empty($dayHistories)) {
                $lastDayHistory = $dayHistories[count($dayHistories) - 1];
                $status = $lastDayHistory->switch_to;
            }

//            echo $status . "($day)" . PHP_EOL;

            if ($status === "online") {
                $count++;
            }
        }
//        echo "</pre>";

        return $count;
    }

    public function histories(): array
    {
        $offer = $this->offer();
        $lastMonthHistories = OfferStatusHistory::query()->filters(['offer_id' => $offer->id])->search(['created_at' => date('Y-m', strtotime( $this->issue_date . "-1 month"))])->make();
        $histories = OfferStatusHistory::query()->filters(['offer_id' => $offer->id])->search(['created_at' => date('Y-m', strtotime($this->issue_date))])->make();
        $lastMonthDay = date('t', strtotime(date('Y-m', strtotime($this->issue_date))));
        $historiesString = [];

        // Set status
        if (empty($lastMonthHistories)) {
            $status = $offer->offline ? "offline" : "online";
        } else {
            $status = $lastMonthHistories[count($lastMonthHistories) - 1]->switch_to;
        }

        for ($day = 0; $day <= $lastMonthDay; $day++) {
            // Check if the status has change on this day
            $dayHistories = array_filter($histories, fn($history) => date('d', strtotime($history->created_at)) == $day);
            $dayHistories = array_values($dayHistories);

            if (!empty($dayHistories)) {
                $lastDayHistory = $dayHistories[count($dayHistories) - 1];
                $status = $lastDayHistory->switch_to;
            }

            // Get the equivalent of the day in the month
            $monthDay = date('d', strtotime($this->issue_date . "-1 month +$day day"));

            $historiesString[$monthDay] = $status;
        }

        return $historiesString;
    }
}