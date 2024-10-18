<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\Offer;
use app\models\OfferTag;

class OfferController extends Controller
{
    public function create(Request $request, Response $response) {
        $offer = new Offer();
        $offerTags = OfferTag::all();

        return $this->render('offers/create', [
            'model' => $offer,
            'offerTags' => $offerTags
        ]);
    }
}