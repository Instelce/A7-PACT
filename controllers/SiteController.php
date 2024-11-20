<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\ContactForm;
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

        $allOffers = Offer::all();
        usort($allOffers, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        $offers = [];
        foreach ($allOffers as $offer) {
            if ($offer["offline"] == Offer::STATUS_ONLINE) {
                $image = OfferPhoto::findOne(['offer_id' => $offer["id"]])->url_photo ?? null;
                $professional = ProfessionalUser::findOne(['user_id' => $offer["professional_id"]])->denomination ?? null;
                $type = null;
                $price = null;

                switch ($offer["category"]) {
                    case 'restaurant':
                        $type = "Restaurant";
                        $offerInfo = RestaurantOffer::findOne(['offer_id' => $offer["id"]]);
                        $rangePrice = $offerInfo->range_price ?? null;
                        $price = $rangePrice === 1 ? "• €" : ($rangePrice === 2 ? "• €€" : "• €€€");
                        break;

                    case 'activity':
                        $type = "Activité";
                        $price = ActivityOffer::findOne(['offer_id' => $offer["id"]])->price ?? null;
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

                $location = Address::findOne(['id' => $offer["address_id"]])->city ?? null;
                $lastOnlineDate = strtotime($offer["last_online_date"] ?? 'now');
                $currentDate = strtotime(date('Y-m-d'));
                $daysSinceOnline = floor(($currentDate - $lastOnlineDate) / (60 * 60 * 24));

                $offers[$offer["id"]] = [
                    "id" => $offer["id"],
                    "image" => $image,
                    "title" => $offer["title"],
                    "author" => $professional,
                    "type" => $type,
                    "price" => $price,
                    "location" => $location,
                    "description" => $offer["description"] ?? "",
                    "days_since_online" => $daysSinceOnline,
                ];
            }
        }

        $params['offers'] = $offers;

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
        usort($allOffers, function ($a, $b) {//sort the offer by created_at
            return strtotime($b->created_at) - strtotime($a->created_at);
        });
        $offers = [];//create final table to send into the vue
        foreach ($allOffers as $offer) {//foreach offer
            if ($offer->offline == Offer::STATUS_ONLINE) {//show only online offer
                $image = OfferPhoto::findOne(['offer_id' => $offer->id])->url_photo ?? NULL;//get the first image of the offer for the preview
                $professional = ProfessionalUser::findOne(where: ['user_id' => $offer->professional_id])->denomination ?? NULL;//get the name of the professionnal who post the offer
                $info = NULL;
                $type = NULL;
                switch ($offer->category) {
                    case 'restaurant':
                        $type = "Restaurant";
                        $OfferInfo = RestaurantOffer::findOne(['offer_id' => $offer->id]) ?? NULL;//get the type unique information
                        $tmp = $OfferInfo->range_price ?? NULL;
                        if ($tmp == 1) {
                            $info = "• €";
                        } elseif ($tmp == 2) {
                            $info = "• €€";
                        } elseif ($tmp == 3) {
                            $info = "• €€€";
                        }
                        break;
                    case 'activity':
                        $type = "Activité";
                        $OfferInfo = ActivityOffer::findOne(['offer_id' => $offer->id]) ?? NULL;//get the type unique information
                        $tmp = $OfferInfo->duration ?? NULL;
                        $tmp2 = $OfferInfo->required_age ?? NULL;
                        if ($tmp) {
                            $str1 = "• " . strval($tmp) . "h ";
                        }
                        if ($tmp2) {
                            $str2 = "• à partir de " . strval($tmp2) . " ans";
                        }
                        $info = $str1 . $str2;
                        break;
                    case 'show':
                        $type = "Spectacle";
                        $OfferInfo = ShowOffer::findOne(['offer_id' => $offer->id]) ?? NULL;//get the type unique information
                        $PeriodInfo = OfferPeriod::findOne(['id' => $OfferInfo->period_id]) ?? NULL;
                        $tmp = $OfferInfo->duration ?? NULL;
                        $start_date = $PeriodInfo->start_date ?? NULL;
                        $end_date = $PeriodInfo->end_date ?? NULL;
                        if ($start_date) {
                            $tmp2 = Utils::formatDate($start_date) ?? NULL;
                        }
                        if ($end_date) {
                            $tmp3 = Utils::formatDate($end_date) ?? NULL;
                        }
                        if ($tmp) {
                            $str1 = "• " . strval($tmp) . "h ";
                        }
                        if ($tmp2 && $tmp3) {
                            $str2 = "• du " . strval($tmp2) . " au " . strval($tmp3);
                        }
                        $info = $str1 . $str2;
                        break;
                    case 'visit':
                        $type = "Visite";
                        $OfferInfo = VisitOffer::findOne(['offer_id' => $offer->id]) ?? NULL;//get the type unique information
                        $PeriodInfo = OfferPeriod::findOne(['id' => $OfferInfo->period_id]) ?? NULL;
                        var_dump($PeriodInfo);
                        $tmp = $OfferInfo->duration ?? NULL;
                        $start_date = $PeriodInfo->start_date ?? NULL;
                        $end_date = $PeriodInfo->end_date ?? NULL;
                        if ($start_date) {
                            $tmp2 = Utils::formatDate($start_date) ?? NULL;
                        }
                        if ($end_date) {
                            $tmp3 = Utils::formatDate($end_date) ?? NULL;
                        }
                        $tmp4 = $OfferInfo->guide ?? NULL;
                        if ($tmp4 == true) {
                            $tmp4 = "• Avec guide";
                        } else {
                            $tmp4 = "• Sans guide";
                        }
                        if ($tmp) {
                            $str1 = "• " . strval($tmp) . "h ";
                        }
                        if ($tmp2 && $tmp3) {
                            $str2 = "• du " . strval($tmp2) . " au " . strval($tmp3);
                        }
                        if ($tmp4) {
                            $str3 = $tmp4;
                        }
                        $info = $str1 . $str2 . $str3;
                        break;
                    case 'attraction_park':
                        $type = "Parc d'attraction";
                        $OfferInfo = AttractionParkOffer::findOne(['offer_id' => $offer->id]) ?? NULL;//get the type unique information
                        $tmp = $OfferInfo->attraction_number ?? NULL;
                        $tmp2 = $OfferInfo->required_age ?? NULL;
                        if ($tmp) {
                            $str1 = "• " . strval($tmp) . " attractions ";
                        }
                        if ($tmp2) {
                            $str2 = "• à partir de " . strval($tmp2) . " ans";
                        }
                        $info = $str1 . $str2;
                        break;
                }
                $location = Address::findOne(['id' => $offer->address_id])->city ?? NULL; // get the city of the offer
                $offers[$offer->id] = [//set up the final array to send to the vue
                    "id" => $offer->id, //id for the link into the detail offer and the traitement of click and vue statistiques
                    "image" => $image,//preview image
                    "title" => $offer->title,
                    "author" => $professional,
                    "type" => $type,
                    "info" => $info,
                    "location" => $location,
                ];
            }
        }
        return $this->render("research", ["offers" => $offers]);
    }
}