<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\account\UserAccount;
use app\models\offer\Offer;
use app\models\opinion\Opinion;
use app\models\opinion\OpinionPhoto;
use app\models\user\MemberUser;
use app\models\offer\AttractionParkOffer;
use app\models\offer\OfferPhoto;
use app\models\offer\ActivityOffer;
use app\models\offer\RestaurantOffer;
use app\models\Address;
use app\models\offer\ShowOffer;
use app\models\offer\OfferPeriod;
use app\models\offer\VisitOffer;
use app\models\user\professional\ProfessionalUser;
use app\core\Utils;

class ApiController extends Controller
{
    /**
     * Get the authenticated user
     */
    public function user(Request $request, Response $response)
    {
        if (!Application::$app->user) {
            $response->setStatusCode(401);
            return $response->json(['error' => 'Not authenticated']);
        }
        $user = Application::$app->user->toJson();
        unset($user['password']);
        return $response->json($user);
    }

    public function offers(Request $request, Response $response)
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
                    "info" => $info
                ];
            }
        }
        return $response->json($offers);
    }

    /**
     * Get the opinions of an offer
     *
     * Params :
     * - offset : int
     * - limit : int
     * - order_by : string (default: created_at)
     */
    public function opinions(Request $request, Response $response)
    {
        $q = $request->getQueryParams('q');
        $offerId = $request->getQueryParams('offer_id');
        $accountId = $request->getQueryParams('account_id');
        $offerProfessionalId = $request->getQueryParams('offer_pro_id');
        $offset = $request->getQueryParams('offset');
        $limit = $request->getQueryParams('limit');
        $orderBy = explode(',', $request->getQueryParams('order_by') ?? '-created_at');

        $data = [];
        $where = [];

        if ($offerId) {
            $where['offer_id'] = $offerId;
        }
        if ($accountId) {
            $where['account_id'] = $accountId;
        }
        if ($offerProfessionalId) {
            $where['offer__professional_id'] = $offerProfessionalId;
        }
        if (is_numeric($q)) {
            $where['rating'] = $q;
            $q = null;
        }

        /** @var Opinion[] $opinions */
        $opinions = Opinion::query()
            ->join(new Offer())
            ->filters($where)
            ->search(["title" => $q])
            ->limit($limit)
            ->offset($offset)
            ->order_by($orderBy)
            ->make();

        foreach ($opinions as $i => $opinion) {
            $data[$i] = $opinion->toJson();

            // Add user account
            $data[$i]['user'] = UserAccount::findOneByPk($opinion->account_id)->toJson();
            unset($data[$i]['user']['password']);

            // And append member user data
            $member = MemberUser::findOneByPk($opinion->account_id)->toJson();
            foreach ($member as $key => $value) {
                $data[$i]['user'][$key] = $value;
            }

            // Add offer data
            $offer = Offer::findOneByPk($opinion->offer_id);
            $data[$i]['offer'] = $offer->toJson();

            // Add photos
            /** @var OpinionPhoto[] $photos */
            $photos = OpinionPhoto::find(['opinion_id' => $opinion->id]);
            $data[$i]['photos'] = [];
            foreach ($photos as $photo) {
                $data[$i]['photos'][] = $photo->toJson();
            }
        }

        return $response->json($data);
    }
}