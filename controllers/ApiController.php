<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\models\account\AnonymousAccount;
use app\models\account\UserAccount;
use app\models\offer\Offer;
use app\models\offer\OfferType;
use app\models\offer\schedule\OfferSchedule;
use app\models\offer\schedule\LinkSchedule;
use app\models\opinion\Opinion;
use app\models\opinion\OpinionDislike;
use app\models\opinion\OpinionLike;
use app\models\opinion\OpinionPhoto;
use app\models\user\MemberUser;
use app\models\offer\AttractionParkOffer;
use app\models\offer\OfferPhoto;
use app\models\offer\ActivityOffer;
use app\models\offer\RestaurantOffer;
use app\models\offer\Subscription;
use app\models\offer\Option;
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
        $query = Offer::query();

        $q = $request->getQueryParams('q');
        $offset = $request->getQueryParams('offset');
        $limit = $request->getQueryParams('limit');
        $enrelief = $request->getQueryParams('enrelief');
        $online = $request->getQueryParams('online');
        $status = $request->getQueryParams('status');
        $enrelief = $request->getQueryParams('enrelief');
        $online = $request->getQueryParams('online');
        if ($enrelief) {
            $order_by = $request->getQueryParams('order_by') ? explode(',', $request->getQueryParams('order_by')) : ['-_est_en_relief'];
        } else {
            $order_by = $request->getQueryParams('order_by') ? explode(',', $request->getQueryParams('order_by')) : ['-created_at'];
        }
        $professional_id = $request->getQueryParams('professional_id');
        $category = $request->getQueryParams('category');
        $minimumPrice = $request->getQueryParams('minimumPrice');
        $maximumPrice = $request->getQueryParams('maximumPrice');
        // $open = $request->getQueryParams('open');
        // $minimumEventDate = $request->getQueryParams('minimumEventDate');
        // $maximumEventDate = $request->getQueryParams('maximumEventDate');
        $location = $request->getQueryParams('location');
        $rating = $request->getQueryParams('rating');
        $rangePrice = $request->getQueryParams('rangePrice');
        $latitude = $request->getQueryParams('latitude');
        $longitude = $request->getQueryParams('longitude');
        $type = $request->getQueryParams('type');
        $option = $request->getQueryParams('option');

        $data = [];
        $where = [];
        if ($online) {
            $where[] = ['offline', "false"];
        }
        if ($status) {
            $where[] = ['offline', $status === 'offline' ? 'true' : 'false'];
        }
        if ($professional_id) {
            $where['professional_id'] = $professional_id;
        }
        if ($category) {
            $where['category'] = $category;
        }
        // if ($location) {
        //     $where['address__city'] = $location;
        // }
        if ($minimumPrice) {
            $where[] = ['minimum_price', $minimumPrice, '>='];
        }
        if ($maximumPrice) {
            $where[] = ['minimum_price', $maximumPrice, '<=', 'maximum_price'];
        }
        if ($rating) {
            $where[] = ['rating', $rating, '>='];
        }
        if ($latitude && $longitude) {
            $latitudePositif = floatval($latitude + 0.3);
            $latitudeNegatif = floatval(value: $latitude - 0.3);
            $longitudePositif = floatval($longitude + 0.3);
            $longitudeNegatif = floatval($longitude - 0.3);
            $latitudePositif = strval($latitudePositif);
            $latitudeNegatif = strval(value: $latitudeNegatif);
            $longitudePositif = strval($longitudePositif);
            $longitudeNegatif = strval($longitudeNegatif);
            $where[] = ['address__latitude', $latitudePositif, '<=', 'latitudepositif'];
            $where[] = ['address__latitude', $latitudeNegatif, '>=', 'latitudenegatif'];
            $where[] = ['address__longitude', $longitudePositif, '<=', 'longitudepositif'];
            $where[] = ['address__longitude', $longitudeNegatif, '>=', 'longitudenegatif'];
        }
        if ($type) {
            $where['offer_type__type'] = $type;
            $query->joinString("JOIN offer_type ON offer_type.id = offer.offer_type_id");
        }
        if ($option) {
            $where['option__type'] = $option;
        }

        if (in_array('price_asc', $order_by)) {
            $order_by = array_diff($order_by, ['price_asc']);
            $order_by[] = 'minimum_price ASC';
            $where[] = ['minimum_price', '0', '>', 'minimum_priceAsc'];
        } else if (in_array('price_desc', $order_by)) {
            $order_by = array_diff($order_by, ['price_desc']);
            $order_by[] = 'minimum_price DESC';
            $where[] = ['minimum_price', '0', '>', 'minimum_priceDesc'];
        } else if (in_array('rating_asc', $order_by)) {
            $order_by = array_diff($order_by, ['rating_asc']);
            $order_by[] = 'rating ASC';
            $where[] = ['rating', '0', '>', 'ratingAsc'];
        } else if (in_array('rating_desc', $order_by)) {
            $order_by = array_diff($order_by, arrays: ['rating_desc']);
            $order_by[] = 'rating DESC';
            $where[] = ['rating', '0', '>', 'ratingDesc'];
        }

        $query->select(attrs: ['offer.*', "(CASE WHEN option.type = 'en_relief' OR option.type = 'a_la_une' THEN 1 ELSE 0 END) as _est_en_relief"])
            ->join(new Address())
            ->joinString("LEFT JOIN subscription ON subscription.offer_id = offer.id")
            ->joinString("LEFT JOIN option ON option.id = subscription.option_id")
            ->limit($limit)
            ->offset($offset)
            ->filters($where)
            ->search(['address__city' => $location, 'title' => $q])
            ->order_by($order_by);

        if ($rangePrice) {
            $query->joinString("JOIN restaurant_offer ON restaurant_offer.offer_id = offer.id")
                ->filters([
                    ['restaurant_offer__range_price', $rangePrice]
                ]);

        }
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
            $openingHours = $offer->schedule();
            $dayOfWeek = strtolower((new DateTime())->format('N'));
            foreach ($openingHours as $openingHour) {
                if ($openingHour->day == $dayOfWeek) {
                    $todayHour = $openingHour;
                }
            }

            $closingHour = $todayHour->closing_hours;
            $openingHour = $todayHour->opening_hours;

            if ($todayHour) {
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
            } else {
                $status = NULL;
            }
            $data[$i]['status'] = $status;

            // Add offer type
            $data[$i]['type'] = OfferType::findOneByPk($offer->offer_type_id)->type;
            unset($data[$i]['offer_type_id']);

            // Add relief and a la une
            $relief = false;
            $a_la_une = false;
            $subscription = $offer->subscription();
            if ($subscription) {
                $option = Option::findOne(['id' => $subscription->option_id]);
                if ($option->type == 'en_relief') {
                    $relief = true;
                }
                if ($option->type == 'a_la_une') {
                    $a_la_une = true;
                }
            }
            $data[$i]['relief'] = $relief;
            $data[$i]['a_la_une'] = $a_la_une;

            // Add subscription and option
            if ($subscription) {
                $data[$i]['subscription'] = $subscription->toJson();
                $data[$i]['subscription']['end_date'] = $subscription->endDate();
                $data[$i]['subscription']['option'] = $subscription->option()->toJson();
            } else {
                $data[$i]['subscription'] = null;
            }
            unset($data[$i]['subscription']['option_id']);
            unset($data[$i]['subscription']['offer_id']);

            // Opinion count
            $data[$i]['opinion_count'] = $offer->opinionsCount();
            $data[$i]['no_read_opinion_count'] = $offer->noReadOpinions();
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

            if (UserAccount::findOneByPk($opinion->account_id)) {
                $data[$i]['user'] = UserAccount::findOneByPk($opinion->account_id)->toJson();
                unset($data[$i]['user']['password']);
                unset($data[$i]['user']['reset_password_hash']);
                unset($data[$i]['user']['api_token']);
                unset($data[$i]['user']['mail']);

                // And append member user data
                $member = MemberUser::findOneByPk($opinion->account_id)->toJson();
                foreach ($member as $key => $value) {
                    $data[$i]['user'][$key] = $value;
                }
            } else {
                $data[$i]['user'] = AnonymousAccount::findOneByPk($opinion->account_id)->toJson();
                $data[$i]['user']['avatar_url'] = "https://static.vecteezy.com/system/resources/previews/001/840/618/original/picture-profile-icon-male-icon-human-or-people-sign-and-symbol-free-vector.jpg";
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

            $data[$i]['likes'] = $opinion->likes();
            $data[$i]['dislikes'] = $opinion->dislikes();

            // Récupérer opinion id
            if (OpinionLike::findOne(["opinion_id" => $opinion->id, "account_id" => Application::$app->user->account_id])) {
                $data[$i]['opinionLiked'] = true;
            } else {
                $data[$i]['opinionLiked'] = false;
            }

            if (OpinionDislike::findOne(["opinion_id" => $opinion->id, "account_id" => Application::$app->user->account_id])) {
                $data[$i]['opinionDisliked'] = true;
            } else {
                $data[$i]['opinionDisliked'] = false;
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

    public function opinionLikes(Request $request, Response $response, $routeParams)
    {
        $action = $request->getBody()["action"];
        $opinionPk = $routeParams['opinion_pk'];
        $opinion = Opinion::findOneByPk($opinionPk);

        if (!$opinion) {
            $response->setStatusCode(404);
            return $response->json(['error' => 'Opinion not found']);
        }

        if ($action == "add") {
            $opinion->addLike();
        } else if ($action == "remove") {
            $opinion->removeLike();
        }

        return $response->json(["action" => $action, "body" => $request->getBody()]);
    }

    public function opinionDislikes(Request $request, Response $response, $routeParams)
    {
        $action = $request->getBody()["action"];
        $opinionPk = $routeParams['opinion_pk'];
        $opinion = Opinion::findOneByPk($opinionPk);

        if (!$opinion) {
            $response->setStatusCode(404);
            return $response->json(['error' => 'Opinion not found']);
        }

        if ($action == "add") {
            $opinion->addDislike();
        } else if ($action == "remove") {
            $opinion->removeDislike();
        }

        return $response->json(["action" => $action, "body" => $request->getBody()]);
    }

    public function opinionReports(Request $request, Response $response, $routeParams)
    {
        $opinionPk = $routeParams['opinion_pk'];
        $opinion = Opinion::findOneByPk($opinionPk);

        if (!$opinion) {
            $response->setStatusCode(404);
            return $response->json(['error' => 'Opinion not found']);
        }

        $opinion->addReport();

        return $response->json([]);
    }

    /**
     * Notifications
     *
     * Params :
     *
     *
     *
     *
     */
    public function notifications(Request $request, Response $response, $routeParams)
    {

        //private $notificationModel;

        public function __construct()
        {
            $this->notificationModel = new Notifications();
        }

        public function createNotification($userId, $content)
        {
            try {
                $this->notificationModel->createNotification($userId, $content);
                return [
                    'status' => 'success',
                    'message' => 'Notification créée avec succès.'
                ];
            } catch (\Exception $e) {
                return [
                    'status' => 'error',
                    'message' => 'Erreur : ' . $e->getMessage()
                ];
            }
        }

        public function markAsRead($notificationId)
        {
            try {
                $this->notificationModel->markAsRead($notificationId);
                return [
                    'status' => 'success',
                    'message' => 'Notification marquée comme lue.'
                ];
            } catch (\Exception $e) {
                return [
                    'status' => 'error',
                    'message' => 'Erreur : ' . $e->getMessage()
                ];
            }
        }

        public function getUnreadNotifications($userId)
        {
            try {
                $notifications = $this->notificationModel->getUnreadNotifications($userId);
                return [
                    'status' => 'success',
                    'data' => $notifications
                ];
            } catch (\Exception $e) {
                return [
                    'status' => 'error',
                    'message' => 'Erreur : ' . $e->getMessage()
                ];
            }
        }
    }
}