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
        $professionnal_id = $request->getQueryParams('professional_id');
        if ($request->getQueryParams('category')) {
            $category = $request->getQueryParams('category');
        }
        if ($request->getQueryParams('minimumOpinions')) {
            $minimumOpinions = $request->getQueryParams('minimumOpinions');
        }
        if ($request->getQueryParams('maximumOpinions')) {
            $maximumOpinions = $request->getQueryParams('maximumOpinions');
        }
        if ($request->getQueryParams('minimumPrice')) {
            $minimumPrice = $request->getQueryParams('minimumPrice');
        }
        if ($request->getQueryParams('maximumPrice')) {
            $maximumPrice = $request->getQueryParams('maximumPrice');
        }
        if ($request->getQueryParams('open')) {
            $open = $request->getQueryParams('open');
        }
        if ($request->getQueryParams('minimumEventDate')) {
            $minimumEventDate = $request->getQueryParams('minimumEventDate');
        }
        if ($request->getQueryParams('maximumEventDate')) {
            $maximumEventDate = $request->getQueryParams('maximumEventDate');
        }
        if ($request->getQueryParams('location')) {
            $location = $request->getQueryParams('location');
        }



        $data = [];
        $where = [];
        if ($professionnal_id) {
            $where['professional_id'] = $professionnal_id;
        }
        if ($category) {
            $where['category'] = $category;
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

        /** @var Offer[] $offers */
        $offers = Offer::query()
            // ->join(new OfferPeriod())
            ->join(new Address())
            ->limit($limit)
            ->offset($offset)
            ->filters($where)
            ->search(['title' => $q])
            ->order_by($order_by)
            ->make();

        foreach ($offers as $i => $offer) {
            $data[$i] = $offer->toJson();

            // Add professionalUser account
            $data[$i]['profesionalUser'] = ProfessionalUser::findOneByPk($offer->professional_id)->toJson();
            unset($data[$i]['profesionalUser']['notification']);
            unset($data[$i]['profesionalUser']['conditions']);

            $data[$i]["photos"] = [];
            foreach ($offer->photos() as $photo) {
                $data[$i]["photos"][] = $photo->url_photo;
            }

            $data[$i]["specific"] = $offer->specificData()->toJson();
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