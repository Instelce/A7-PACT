<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\ContactForm;
use app\models\offer\Offer;
use app\models\offer\OfferPhoto;
use app\models\offer\ActivityOffer;
use app\models\offer\RestaurantOffer;
use app\models\Address;
use app\models\offer\ShowOffer;
use app\models\user\professional\ProfessionalUser;

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
    public function research()//render research
    {
        $allOffers = Offer::all();//get all offer from the model
        $offers = [];//create final table to send into the vue
        foreach ($allOffers as $offer) {//foreach offer
            if ($offer["offline"] == Offer::STATUS_ONLINE) {//show only online offer
                $image = OfferPhoto::findOne(['offer_id' => $offer["id"]])->url_photo ?? NULL;//get the first image of the offer for the preview
                $professional = ProfessionalUser::findOne(where: ['user_id' => $offer["professional_id"]])->denomination ?? NULL;//get the name of the professionnal who post the offer
                $price = -1;
                $type = NULL;
                switch ($offer["category"]) {
                    case 'restaurant':
                        $type = "Restaurant";
                        // $price = RestaurantOffer::findOne(['offer_id' => $offer["id"]])->minimum_price ?? NULL;
                        break;
                    case 'activity':
                        $type = "Activité";
                        // $price = ActivityOffer::findOne(['offer_id' => $offer["id"]])->price ?? NULL;
                        break;
                    case 'show':
                        $type = "Spectacle";
                        break;
                    case 'visit':
                        $type = "Visite";
                        break;
                    case 'attraction_park':
                        $type = "Parc d'attraction";
                        break;
                }
                $location = Address::findOne(['id' => $offer["address_id"]])->city ?? NULL; // get the city of the offer
                $offers[$offer["id"]] = [//set up the final array to send to the vue
                    "id" => $offer["id"], //id for the link into the detail offer and the traitement of click and vue statistiques
                    "image" => $image,//preview image
                    "title" => $offer["title"],
                    "author" => $professional,
                    "type" => $type,
                    "price" => $price,
                    "location" => $location,
                    "date" => $offer["last_online_date"],
                ];
            }
        }
        return $this->render("research", ["offers" => $offers]);
    }
}