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
        $allOffers = Offer::all();  // Get all offers from the model
        return $this->json($allOffers);
    }

    /**
     * Get the opinions of an offer
     *
     * Params :
     * - offset : int
     * - limit : int
     */
    public function opinions(Request $request, Response $response, $routeParams)
    {
        $offerId = $routeParams['offer_id'];
        $offset = $request->getQueryParams('offset');
        $limit = $request->getQueryParams('limit');

        $data = [];

        /** @var Opinion[] $opinions */
        $opinions = Opinion::query()->filters(['offer_id' => $offerId])->limit($limit)->offset($offset)->make();

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

            // Add photos
            /** @var OpinionPhoto[] $photos */
            $photos = OpinionPhoto::find(['opinion_id' => $opinion->id]);
            foreach ($photos as $photo) {
                $data[$i]['photos'][] = $photo->toJson();
            }
        }

        return $response->json($data);
    }
}