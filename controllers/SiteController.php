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

        $offers = [];
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
                        $rangePrice = $offerInfo->range_price ?? null;
                        $price = $rangePrice === 1 ? "Dès €" : ($rangePrice === 2 ? "Dès €€" : "Dès €€€");
                        break;

                    case 'activity':
                        $type = "Activité";
                        $priceData = ActivityOffer::findOne(['offer_id' => $offer->id]);
                        $price = $priceData && $priceData->price !== null ? "Dès " . $priceData->price . "/Personne" : "Gratuit";
                        break;

                    case 'show':
                        $type = "Spectacle";
                        $priceData = ShowOffer::findOne(['offer_id' => $offer->id]);
                        $price = $priceData && $priceData->price !== null ? "Dès " . $priceData->price . "/Personne" : "Gratuit";
                        break;

                    case 'visit':
                        $type = "Visite";
                        $priceData = VisitOffer::findOne(['offer_id' => $offer->id]);
                        $price = $priceData && $priceData->price !== null ? "Dès " . $priceData->price . "/Personne" : "Gratuit";
                        break;

                    case 'attraction_park':
                        $priceData = AttractionParkOffer::findOne(['offer_id' => $offer->id]);
                        $price = $priceData && $priceData->price !== null ? "Dès " . $priceData->price . "/Personne" : "Gratuit";
                        $type = "Parc d'attraction";
                        break;
                }

                $location = Address::findOne(['id' => $offer->address_id])->city ?? null;
                $lastOnlineDate = strtotime($offer->last_online_date ?? 'now');
                $currentDate = strtotime(date('Y-m-d'));
                $dateSincePublication = floor(($currentDate - $lastOnlineDate) / (60 * 60 * 24));

                if ($offer -> isALaUne()){
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
                    ];
                }

                $offers[$offer->id] = [
                    "id" => $offer->id,
                    "image" => $image,
                    "title" => $offer->title,
                    "author" => $professional,
                    "type" => $type,
                    "price" => $price,
                    "location" => $location,
                    'summary' => $offer->summary,
                    "dateSincePublication" => $dateSincePublication,
                ];
            }
        }

        $params = [
            'offers' => $offers,
            'offersALaUne' => $offersALaUne,
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
        usort($allOffers, function ($a, $b) {//sort the offer by created_at
            return strtotime($b->created_at) - strtotime($a->created_at);
        });
        $offers = [];//create final table to send into the vue
        foreach ($allOffers as $offer) {//foreach offer
            if ($offer->offline == Offer::STATUS_ONLINE) {//show only online offer
                $offers[$offer->id] = [//set up the final array to send to the vue
                    "id" => $offer->id
                ]; //id for the link into the detail offer and the traitement of click and vue statistiques

                $image = OfferPhoto::findOne(['offer_id' => $offer->id])->url_photo ?? NULL;//get the first image of the offer for the preview
                $professional = ProfessionalUser::findOne(where: ['user_id' => $offer->professional_id])->denomination ?? NULL;//get the name of the professionnal who post the offer
                $info = [];
                $type = NULL;
                switch ($offer->category) {
                    case 'restaurant':
                        $type = "Restaurant";
                        $OfferInfo = RestaurantOffer::findOne(['offer_id' => $offer->id]) ?? NULL;//get the type unique information
                        $tmp = $OfferInfo->range_price ?? NULL;
                        if ($tmp && $tmp != 0) {
                            $info += [//set up the final array to send to the vue
                                "range_price" => $tmp
                            ];
                        }
                        break;
                    case 'activity':
                        $type = "Activité";
                        $OfferInfo = ActivityOffer::findOne(['offer_id' => $offer->id]) ?? NULL;//get the type unique information
                        $tmp = $OfferInfo->duration ?? NULL;
                        $tmp2 = $OfferInfo->required_age ?? NULL;
                        if ($tmp && $tmp != 0) {
                            $info += [//set up the final array to send to the vue
                                "duration" => $tmp
                            ];
                        }
                        if ($tmp2 && $tmp2 != 0) {
                            $info += [//set up the final array to send to the vue
                                "required_age" => $tmp2
                            ];
                        }
                        break;
                    case 'show':
                        $type = "Spectacle";
                        $OfferInfo = ShowOffer::findOne(['offer_id' => $offer->id]) ?? NULL;//get the type unique information
                        $PeriodInfo = OfferPeriod::findOne(['id' => $OfferInfo->period_id]) ?? NULL;
                        $tmp = $OfferInfo->duration ?? NULL;
                        $start_date = $PeriodInfo->start_date ?? NULL;
                        $end_date = $PeriodInfo->end_date ?? NULL;
                        if ($start_date && $start_date != 0) {
                            $info += [//set up the final array to send to the vue
                                "start_date" => Utils::formatDate($start_date)
                            ];
                        }
                        if ($end_date && $end_date != 0) {
                            $info += [//set up the final array to send to the vue
                                "end_date" => Utils::formatDate($end_date)
                            ];
                        }
                        if ($tmp && $tmp != 0) {
                            $info += [//set up the final array to send to the vue
                                "duration" => $tmp
                            ];
                        }
                        break;
                    case 'visit':
                        $type = "Visite";
                        $OfferInfo = VisitOffer::findOne(['offer_id' => $offer->id]) ?? NULL;//get the type unique information
                        $PeriodInfo = OfferPeriod::findOne(['id' => $OfferInfo->period_id]) ?? NULL;
                        $tmp = $OfferInfo->duration ?? NULL;
                        $tmp2 = $OfferInfo->guide ?? NULL;
                        $start_date = $PeriodInfo->start_date ?? NULL;
                        $end_date = $PeriodInfo->end_date ?? NULL;
                        if ($start_date && $start_date != 0) {
                            $info += [//set up the final array to send to the vue
                                "start_date" => Utils::formatDate($start_date)
                            ];
                        }
                        if ($end_date && $end_date != 0) {
                            $info += [//set up the final array to send to the vue
                                "end_date" => Utils::formatDate($end_date)
                            ];
                        }
                        if ($tmp2 && $tmp2 != 0) {
                            $info += [//set up the final array to send to the vue
                                "guide" => $tmp2
                            ];
                        }
                        if ($tmp && $tmp != 0) {
                            $info += [//set up the final array to send to the vue
                                "duration" => $tmp
                            ];
                        }
                        break;
                    case 'attraction_park':
                        $type = "Parc d'attraction";
                        $OfferInfo = AttractionParkOffer::findOne(['offer_id' => $offer->id]) ?? NULL;//get the type unique information
                        $tmp = $OfferInfo->attraction_number ?? NULL;
                        $tmp2 = $OfferInfo->required_age ?? NULL;
                        if ($tmp && $tmp != 0) {
                            $info += [//set up the final array to send to the vue
                                "attraction_number" => $tmp
                            ];
                        }
                        if ($tmp2 && $tmp2 != 0) {
                            $info += [//set up the final array to send to the vue
                                "required_age" => $tmp2
                            ];
                        }
                        break;
                }
                $location = Address::findOne(['id' => $offer->address_id])->city ?? NULL; // get the city of the offer
                $offers[$offer->id] += [//set up the final array to send to the vue
                    "image" => $image,//preview image
                    "title" => $offer->title,
                    "author" => $professional,
                    "type" => $type,
                    "location" => $location,
                    "summary" => $offer->summary ?? "",
                    "minimum_price" => $offer->minimum_price,
                    "info" => $info
                ];
            }
        }
        return $this->render("research", ["offers" => $offers]);
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
        $this->pdf('super-pdf', 'test');
    }
}