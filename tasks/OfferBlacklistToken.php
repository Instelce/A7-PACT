<?php

namespace app\tasks;

use app\core\CronTask;
use app\core\Utils;
use app\models\offer\Offer;
use app\models\payment\Invoice;

class OfferBlacklistToken extends CronTask
{
    public function getName(): string
    {
        return "offer-blacklist-token";
    }

    public function getDescription(): string
    {
        return "Update offer blacklist token automatically";
    }

    public function run(): void
    {
        /** @var Offer[] $offers */
        $offers = Offer::all();
        foreach($offers as $offer){
            if($offer->token_number < 3){
                $offer->token_number++;
                $offer->update();
            }
//            $liste = [];
//            $liste[]=5;
//            for ($i=0; $i<5; $i++){
//                $offer->time_new_token = $liste[0];
//                for($y = 0; $y<count($liste); $y++){
//                    $liste[$y]--;
//                }
//            }
//            $offer->token_number++;
//            $offer->update();
        }
    }

    public function runCondition(): bool
    {
        return true;
    }

    public function schedule(): string
    {
        return "0 0 28-31 * *";
    }
}