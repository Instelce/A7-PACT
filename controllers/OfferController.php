<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\offer\Offer;

class OfferController extends Controller
{
    public function create(Request $request, Response $response) {
        $offer = new Offer();

        if ($request->isPost()) {
            echo "<pre>";
            var_dump($_FILES);
            var_dump($request->getBody());
            echo "</pre>";
            exit;

            $offer->loadData($request->getBody());
        }

        return $this->render('offers/create', [
            'model' => $offer,
        ]);
    }
}