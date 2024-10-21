<?php

namespace app\controllers;

use app\core\Controller;
use app\core\middlewares\AuthMiddleware;
use app\core\Request;
use app\core\Response;
use app\models\offer\Offer;
use app\models\offer\OfferType;

class OfferController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['create']));
    }

    public function create(Request $request, Response $response) {
        $offer = new Offer();

        if ($request->isPost()) {
            echo "<pre>";
            var_dump($_FILES);
            var_dump($request->getBody());
            echo "</pre>";

            // Retrieve the offer type
            $offer_type = OfferType::findOne(['type' => $request->getBody()['type']]);

            // Create the offer
            $offer->loadData($request->getBody());
            $offer->offline_date = date('Y-m-d');
            $offer->last_online_date = null;

            if ($offer->validate()) {
                echo "Offer is valid";
            }

            if ($offer->save()) {
                echo "Offer created successfully";
            }

            exit;
        }

        return $this->render('offers/create', [
            'model' => $offer,
        ]);
    }


    public function detail(Request $request, Response $response){
        return $this->render('offers/detail');
    }
}