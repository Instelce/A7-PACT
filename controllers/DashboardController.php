<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\middlewares\AuthMiddleware;
use app\core\middlewares\BackOfficeMiddleware;
use app\core\Request;
use app\core\Response;
use app\models\offer\Offer;
use app\models\offer\OfferOption;
use app\models\offer\OfferPhoto;
use app\models\offer\OfferType;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['offers']));
        $this->registerMiddleware(new BackOfficeMiddleware(['offers']));
    }

    public function offers(Request $request, Response $response)
    {
        $this->setLayout('back-office');
        $offers = Offer::find(['professional_id' => Application::$app->user->account_id]);

        $photos = [];
        foreach ($offers as $offer) {
            $photos[] = OfferPhoto::findOne(['offer_id' => $offer->id]);
        }

        $offersType = [];
        foreach ($offers as $offer) {
            $offersType[] = OfferType::findOne(['id' => $offer->offer_type_id]);
        }

        $offersOption = [];
        foreach ($offers as $offer) {
            $offersOption[] = OfferOption::findOne(['id' => $offer->offer_option]);
        }

        return $this->render('/dashboard/offres', [
            'offers' => $offers,
            'photos' => $photos,
            'offersType' => $offersType,
            'offersOption' => $offersOption
        ]);
    }
    public function avis(Request $request, Response $response)
    {
        $this->setLayout('back-office');
        return $this->render('/dashboard/avis');
    }
    public function factures(Request $request, Response $response)
    {
        $this->setLayout('back-office');
        return $this->render('/dashboard/factures');
    }
}