<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Model;
use app\core\Request;
use app\core\Response;
use app\models\account\UserAccount;
use app\models\offer\AttractionParkOffer;
use app\models\offer\Offer;
use app\models\offer\OfferPhoto;
use app\models\offer\ActivityOffer;
use app\models\offer\RestaurantOffer;
use app\models\Address;
use app\models\offer\ShowOffer;
use app\models\offer\OfferPeriod;
use app\models\offer\VisitOffer;
use app\models\user\professional\ProfessionalUser;
use app\core\Utils;

class SiteController extends Controller
{
    public function home(Request $request)
    {
        /** @var Offer[] $allOffers */
        $allOffers = Offer::all();
        usort($allOffers, function ($a, $b) {
            return strtotime($b->created_at - strtotime($a->created_at));
        });

        $offersALaUne = [];

        foreach ($allOffers as $offer) {
            if ($offer->offline == Offer::STATUS_ONLINE) {
                $image = OfferPhoto::findOne(['offer_id' => $offer->id])->url_photo ?? null;
                $professional = ProfessionalUser::findOne(['user_id' => $offer->professional_id])->denomination ?? null;
                $type = null;
                $price = null;

                switch ($offer->category) {
                    case 'restaurant':
                        $type = "Restaurant";
                        $range_price = RestaurantOffer::findOne(['offer_id' => $offer->id])->range_price ?? 0;
                        if ($range_price == 1) {
                            $price = "€";
                        } elseif ($range_price == 2) {
                            $price = "€€";
                        } elseif ($range_price == 3) {
                            $price = "€€€";
                        } else {
                            $price = $range_price;
                        }
                        break;

                    case 'activity':
                        $type = "Activité";
                        $priceData = ActivityOffer::findOne(['offer_id' => $offer->id]);
                        $price = $offer->minimum_price !== null ? "À partir de  " . $offer->minimum_price . "€" : "Gratuit";
                        break;

                    case 'show':
                        $type = "Spectacle";
                        $priceData = ShowOffer::findOne(['offer_id' => $offer->id]);
                        $price = $offer->minimum_price !== null ? "À partir de " . $offer->minimum_price . "€" : "Gratuit";
                        break;

                    case 'visit':
                        $type = "Visite";
                        $priceData = VisitOffer::findOne(['offer_id' => $offer->id]);
                        $price = $offer->minimum_price !== null ? "À partir de " . $offer->minimum_price . "€" : "Gratuit";
                        break;

                    case 'attraction_park':
                        $priceData = AttractionParkOffer::findOne(['offer_id' => $offer->id]);
                        $price = $offer->minimum_price !== null ? "À partir de " . $offer->minimum_price . "€" : "Gratuit";
                        $type = "Parc d'attraction";
                        break;
                }


                $location = Address::findOne(['id' => $offer->address_id])->city ?? null;
                $lastOnlineDate = strtotime($offer->last_online_date ?? 'now');
                $currentDate = strtotime(date('Y-m-d'));
                $dateSincePublication = floor(($currentDate - $lastOnlineDate) / (60 * 60 * 24));

                $ratingsCount = Offer::findOne(['id' => $offer->id])->opinionsCount();

                if ($offer->isALaUne()) {
                    $offersALaUne[$offer->id] = [
                        "id" => $offer->id,
                        "image" => $image,
                        "title" => $offer->title,
                        "author" => $professional,
                        "type" => $type,
                        "price" => $price,
                        "location" => $location,
                        'summary' => $offer->summary,
                        "dateSincePublication" => $dateSincePublication,
                        "ratingsCount" => $ratingsCount,
                        'rating' => $offer->rating,
                    ];
                }

//                $offers[$offer->id] = [
//                    "id" => $offer->id,
//                    "image" => $image,
//                    "title" => $offer->title,
//                    "author" => $professional,
//                    "type" => $type,
//                    "price" => $price,
//                    "location" => $location,
//                    'summary' => $offer->summary,
//                    "dateSincePublication" => $dateSincePublication,
//                    "ratingsCount" => $ratingsCount,
//                    'rating' => $offer->rating,
//                ];
            }
        }

        $newOffersArray = [];
        $newOffers = Offer::query()->order_by(["created_at DESC"])->limit(10)->make();

        foreach ($newOffers as $i => $offer) {
            $image = OfferPhoto::findOne(['offer_id' => $offer->id])->url_photo ?? null;
            $professional = ProfessionalUser::findOne(['user_id' => $offer->professional_id])->denomination ?? null;
            $type = null;
            $price = null;

            switch ($offer->category) {
                case 'restaurant':
                    $type = "Restaurant";
                    $range_price = RestaurantOffer::findOne(['offer_id' => $offer->id])->range_price ?? 0;
                    if ($range_price == 1) {
                        $price = "€";
                    } elseif ($range_price == 2) {
                        $price = "€€";
                    } elseif ($range_price == 3) {
                        $price = "€€€";
                    } else {
                        $price = $range_price;
                    }
                    break;

                case 'activity':
                    $type = "Activité";
                    $priceData = ActivityOffer::findOne(['offer_id' => $offer->id]);
                    $price = $offer->minimum_price !== null ? "À partir de  " . $offer->minimum_price . "€" : "Gratuit";
                    break;

                case 'show':
                    $type = "Spectacle";
                    $priceData = ShowOffer::findOne(['offer_id' => $offer->id]);
                    $price = $offer->minimum_price !== null ? "À partir de " . $offer->minimum_price . "€" : "Gratuit";
                    break;

                case 'visit':
                    $type = "Visite";
                    $priceData = VisitOffer::findOne(['offer_id' => $offer->id]);
                    $price = $offer->minimum_price !== null ? "À partir de " . $offer->minimum_price . "€" : "Gratuit";
                    break;

                case 'attraction_park':
                    $priceData = AttractionParkOffer::findOne(['offer_id' => $offer->id]);
                    $price = $offer->minimum_price !== null ? "À partir de " . $offer->minimum_price . "€" : "Gratuit";
                    $type = "Parc d'attraction";
                    break;
            }

            $location = Address::findOne(['id' => $offer->address_id])->city ?? null;
            $lastOnlineDate = strtotime($offer->last_online_date ?? 'now');
            $currentDate = strtotime(date('Y-m-d'));
            $dateSincePublication = floor(($currentDate - $lastOnlineDate) / (60 * 60 * 24));

            $ratingsCount = Offer::findOne(['id' => $offer->id])->opinionsCount();

            $newOffersArray[$offer->id] = [
                "id" => $offer->id,
                "image" => $image,
                "title" => $offer->title,
                "author" => $professional,
                "type" => $type,
                "price" => $price,
                "location" => $location,
                'summary' => $offer->summary,
                "dateSincePublication" => $dateSincePublication,
                "ratingsCount" => $ratingsCount,
                'rating' => $offer->rating,
            ];
        }

        $params = [
            'offersALaUne' => $offersALaUne,
            'newOffers' => $newOffersArray
        ];

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
        $MaxMinimumPrice = 0; // Assign a default value to $MaxMinimumPrice
        foreach ($allOffers as $key => $offer) {
            if ($offer->minimum_price > $MaxMinimumPrice) {
                $MaxMinimumPrice = $offer->minimum_price;
            }
        }

        $researchInfo = [
            "MaxMinimumPrice" => $MaxMinimumPrice
        ];

        return $this->render("research", ["researchInfo" => $researchInfo]);
    }

    /**
     * Only for development purposes
     */
    public function users()
    {
        $users = UserAccount::all();
        Application::$app->mailer->send("test@example.com", "Test", "welcome", ["pseudo" => "Célestin"]);
        return $this->render("auth/users", ["users" => $users]);
    }

    public function testPdf()
    {
        return $this->pdf('super-pdf', 'test');

    }

    public function termofuse()
    {
        return $this->render('termofuse');
    }

    public function mentions()
    {
        return $this->render('mentions');
    }
}