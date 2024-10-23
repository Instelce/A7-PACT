<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\offer\Offer;
use app\models\offer\OfferPhoto;
use app\models\offer\OfferType;

class DashboardController extends Controller
{
    public function offers(Request $request, Response $response) {
        $this->setLayout('back-office');
        $offers = Offer::find(['id'=> Application::$app->user->account_id]);

        $photos = [];
        foreach ($offers as $offer) {
            $photos[] = OfferPhoto::findOne(['offer_id'=>$offer->id]);
        }

        $offersType = [];
        foreach ($offers as $offer) {
            $offersType[] = OfferType::findOne(['id'=>$offer->offer_type_id]);
        }

        return $this->render('/dashboard/offres', [
            'offers' => $offers ,
            'photos' => $photos ,
            'offersType' => $offersType
        ]);
    }
}