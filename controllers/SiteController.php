<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\ContactForm;
use app\models\offer\Offer;
use app\models\offer\OfferPhoto;
use app\models\offer\OfferType;
use app\models\Address;

class SiteController extends Controller
{
    public function home(Request $request)
    {
        $params = [
            "name" => "Foufouille",
            "value" => "1"
        ];
        if ($request->isPost()) {
            $data = $request->getBody();

            $params = [
                "name" => $data['name'],
                "value" => $data['value']
            ];
        }

        return $this->render("home", $params);
    }

    public function contact(Request $request, Response $response)
    {
        $contactForm = new ContactForm();

        if ($request->isPost()) {
            $contactForm->loadData($request->getBody());

            if ($contactForm->validate() && $contactForm->send()) {
                Application::$app->session->setFlash('success', 'Thanks for contacting us');
                $response->redirect('/contact');
            }
        }

        return $this->render("contact", ['model' => $contactForm]);
    }

    public function storybook()
    {
        if ($_ENV['APP_ENVIRONMENT'] === 'dev') {
            $this->setLayout("blank");
            return $this->render("storybook");
        } else {
            return $this->render("404");
        }
    }
    public function research()
    {
        $STATUS_ONLINE = 0;
        $STATUS_OFFLINE = 1;
        $allOffers = Offer::all();
        $offers = [];
        foreach ($allOffers as $offer) {
            if ($offer["offline"] == $STATUS_OFFLINE) {

            }
            $image = OfferPhoto::find(['offer_id' => $offer["id"]])[0]['url_photo'] ?? NULL;
            $type = OfferType::find(['type' => $offer["offer_type_id"]])[0]['type'] ?? NULL;
            $price = OfferType::find(['price' => $offer["offer_type_id"]])[0]['price'] ?? NULL;
            $location = isset($offer["address_id"]) ? Address::find(['id' => $offer["address_id"]])[0]['city'] ?? NULL : NULL;
            $offers[$offer["id"]] = [
                "id" => $offer["id"],
                "image" => $image,
                "title" => $offer["title"],
                "author" => $offer["professional_id"],
                "type" => $type,
                "price" => $price,
                "location" => $location,
                // "locationDistance" => x,
                "date" => $offer["last_online_date"],
            ];
        }
        return $this->render("research", ["offers" => $offers]);
    }
}