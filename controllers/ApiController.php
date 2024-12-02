<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\account\UserAccount;
use app\models\offer\Offer;
use app\models\offer\schedule\OfferSchedule;
use app\models\offer\schedule\LinkSchedule;
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
use DateTime;
use DateInterval;


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

    /**
     * Get all the offers
     *
     * Params :
     * - q : string Research
     * - offset : int
     * - limit : int
     * - order_by : string (default: -created_at)
     */
    public function offers(Request $request, Response $response)
    {
        $q = $request->getQueryParams('q');
        $offset = $request->getQueryParams('offset');
        $limit = $request->getQueryParams('limit');
        $order_by = $request->getQueryParams('order_by') ? explode(',', $request->getQueryParams('order_by')) : ['-created_at'];
        $professional_id = $request->getQueryParams('professional_id');
        $category = $request->getQueryParams('category');
        $minimumPrice = $request->getQueryParams('minimumPrice');
        $maximumPrice = $request->getQueryParams('maximumPrice');
        // $open = $request->getQueryParams('open');
        // $minimumEventDate = $request->getQueryParams('minimumEventDate');
        // $maximumEventDate = $request->getQueryParams('maximumEventDate');
        $location = $request->getQueryParams('location');
        $rating = $request->getQueryParams('rating');

        $data = [];
        $where = [];
        if ($professional_id) {
            $where['professional_id'] = $professional_id;
        }
        if ($category) {
            $where['category'] = $category;
        }
        if ($location) {
            $where['address__city'] = $location;
        }
        if ($minimumPrice) {
            $where[] = ['minimum_price', $minimumPrice, '>='];
        }
        if ($maximumPrice) {
            $where[] = ['minimum_price', $maximumPrice, '<=', 'maximum_price'];
        }

        // if ($minimumEventDate) {
        //     $where[] = ['OfferPeriod__end_date', $minimumEventDate, '<='];
        // }
        // if ($maximumEventDate) {
        //     $where[] = ['OfferPeriod__start_date', $maximumEventDate, '>=', 'maximum_event_date'];
        // }

        if (in_array('price_asc', $order_by)) {
            $order_by = array_diff($order_by, ['price_asc']);
            $order_by[] = 'minimum_price ASC';
            $where[] = ['minimum_price', '0', '>', 'minimum_priceAsc'];
        } else if (in_array('price_desc', $order_by)) {
            $order_by = array_diff($order_by, ['price_desc']);
            $order_by[] = 'minimum_price DESC';
            $where[] = ['minimum_price', '0', '>', 'minimum_priceDesc'];
        }


        $query = Offer::query()
            // ->join(new OfferPeriod())
            //->join(new OfferSchedule())
            ->join(new Address())
            ->limit($limit)
            ->offset($offset)
            ->filters($where)
            ->search(['title' => $q])
            ->order_by($order_by);

        // Calculate the average rating
        if ($rating) {
            $query->joinString("LEFT JOIN opinion ON opinion.offer_id = offer.id")
                ->group_by(['offer.id'])
                ->having('AVG(opinion.rating) >= ' . $rating);
        }
        // if ($open) {
        //     $query->joinString("INNER JOIN link_schedule ON link_schedule.offer_id = offer.id")
        //         ->joinString("INNER JOIN offer_schedule ON offer_schedule.id = link_schedule.schedule_id")
        //         ->filters([
        //             ['offer_schedule__opening_hours', 'fermé', '!='],
        //             ['offer_schedule__closing_hours', 'fermé', '!='],
        //             ['offer_schedule__opening_hours', date('H:i'), '<='],
        //             ['offer_schedule__closing_hours', date('H:i'), '>=']
        //         ]);
        // }

        /** @var Offer[] $offers */
        $offers = $query->make();

        foreach ($offers as $i => $offer) {
            $data[$i] = $offer->toJson();

            $data[$i]['rating'] = $offer->rating();

            // Add professionalUser account
            $data[$i]['profesionalUser'] = ProfessionalUser::findOneByPk($offer->professional_id)->toJson();
            unset($data[$i]['profesionalUser']['notification']);
            unset($data[$i]['profesionalUser']['conditions']);

            $data[$i]["photos"] = [];
            foreach ($offer->photos() as $photo) {
                $data[$i]["photos"][] = $photo->url_photo;
            }

            $data[$i]["specific"] = $offer->specificData()->toJson();

            // Add address
            $address = Address::findOneByPk($offer->address_id);
            $data[$i]['address'] = $address->toJson();
            unset($data[$i]['address']['id']);

            //add status
            $linkSchedules = LinkSchedule::find(['offer_id' => $offer->id]);
            $dayOfWeek = strtolower((new DateTime())->format('N'));
            foreach ($linkSchedules as $linkSchedule) {
                $offerSchedules = OfferSchedule::find(['id' => $linkSchedule->schedule_id]);
                foreach ($offerSchedules as $offerSchedule) {
                    if ($offerSchedule->day == $dayOfWeek) {
                        $closingHour = $offerSchedule->closing_hours;
                        $openingHour = $offerSchedule->opening_hours;
                    }
                }
            }
            if ($closingHour === 'fermé') {
                $status = "Fermé";
            } else {
                $closingTime = new DateTime($closingHour);
                $openingTime = new DateTime($openingHour);

                $currentTime = new DateTime();

                if ($closingTime <= $currentTime && $openingTime >= $currentTime) {
                    $status = "Fermé";
                } elseif ($closingTime <= (clone $currentTime)->add(new DateInterval('PT30M'))) {
                    $status = "Ferme bientôt";
                } else {
                    $status = "Ouvert";
                }
            }
            $data[$i]['status'] = $status;
        }
        return $response->json($data);
    }

    /**
     * Get the opinions of an offer
     *
     * Params :
     * - offset : int
     * - limit : int
     * - order_by : string (default: -created_at)
     */
    public function opinions(Request $request, Response $response)
    {
        $q = $request->getQueryParams('q');
        $offerId = $request->getQueryParams('offer_id');
        $accountId = $request->getQueryParams('account_id');
        $offerProfessionalId = $request->getQueryParams('offer_pro_id');
        $offset = $request->getQueryParams('offset');
        $limit = $request->getQueryParams('limit');
        $orderBy = explode(',', string: $request->getQueryParams('order_by') ?? '-created_at');
        $readOnLoad = $request->getQueryParams('read_on_load');
        $read = $request->getQueryParams('read');

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
        if ($read && $read !== 'null') {
            $where['read'] = $read;
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

            // Read on load
            if ($readOnLoad) {
                $opinion->read = true;
                $opinion->update();
            }
        }

        return $response->json($data);
    }

    /**
     * Update the opinion of an offer
     */
    public function opinionUpdate(Request $request, Response $response, $routeParams)
    {
        $opinionPk = $routeParams['opinion_pk'];

        $opinion = Opinion::findOneByPk($opinionPk);

        if (!$opinion) {
            $response->setStatusCode(404);
            return $response->json(['error' => 'Opinion not found']);
        }

        $opinion->loadData($request->getBody());

        if ($opinion->update()) {
            return $response->json($opinion->toJson());
        }
    }

}