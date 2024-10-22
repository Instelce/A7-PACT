<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\middlewares\AuthMiddleware;
use app\core\middlewares\BackOfficeMiddleware;
use app\core\Request;
use app\core\Response;
use app\models\Address;
use app\models\offer\Offer;
use app\models\offer\OfferTag;
use app\models\offer\OfferType;

class OfferController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['create']));
        $this->registerMiddleware(new BackOfficeMiddleware(['create']));
    }

    public function create(Request $request, Response $response) {
        $this->setLayout('back-office');
        $offer = new Offer();

        if ($request->isPost()) {
            $body = $request->getBody();
            echo "<pre>";
            var_dump($_FILES);
            var_dump($request->getBody());
            echo "</pre>";

            // Retrieve the offer type
            $offer_type = OfferType::findOne(['type' => $request->getBody()['type']]);

            // Create the address
            $address = new Address();
            $address->number = intval($body['address-number']);
            $address->street = $body['address-street'];
            $address->postal_code = $body['address-postal-code'];
            $address->city = $body['address-city'];
            $address->save();

            // Create the offer
            $offer->loadData($request->getBody());
            $offer->offline_date = date('Y-m-d');
            $offer->last_online_date = null;
            $offer->professional_id = Application::$app->user->account_id;
            $offer->offer_type_id = $offer_type->id;
            $offer->address_id = $address->id;

            if ($offer->validate()) {
                echo "Offer is valid";
            }

            if ($offer->save()) {
                echo "Offer created successfully";
            }

            // Add tags to the offer
            foreach ($body['tags'] as $tag) {
                $tag = strtolower($tag);
                $tagModel = OfferTag::findOne(['name' => $tag]);
                $offer->addTag($tagModel->id);
            }

            // Save photos
            $photos = $_FILES['photos'];
            foreach ($photos['name'] as $i => $fileName) {
                $extension = explode('.', $fileName)[1];
                $tmpFilePath = $photos['tmp_name'][$i];
                $fileName = time() . rand(1, 1000) . '.' . $extension;
                $filePath = Application::$ROOT_DIR . '/public/upload/offers/' . $fileName;

                // Save the file
                move_uploaded_file($tmpFilePath, $filePath);

                $offer->addPhoto('/upload/offers/' . $fileName);
            }

            exit;
        }

        return $this->render('offers/create', [
            'model' => $offer,
        ]);
    }

    public function detail(Request $request, Response $response, $pk) {
        return $this->render('offers/detail', [
            'pk' => $pk,
        ]);
    }
}