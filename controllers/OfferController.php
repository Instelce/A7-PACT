<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\middlewares\AuthMiddleware;
use app\core\middlewares\BackOfficeMiddleware;
use app\core\Request;
use app\core\Response;
use app\models\Address;
use app\models\offer\ActivityOffer;
use app\models\offer\Offer;
use app\models\offer\OfferPeriod;
use app\models\offer\OfferSchedule;
use app\models\offer\OfferTag;
use app\models\offer\OfferType;
use app\models\offer\OfferPhoto;
use app\models\user\professional\ProfessionalUser;
use app\models\offer\RestaurantOffer;
use app\models\offer\ShowOffer;
use app\models\offer\VisitOffer;

class OfferController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['create']));
        $this->registerMiddleware(new BackOfficeMiddleware(['create']));
    }

    public function create(Request $request, Response $response) {
        $this->setLayout('back-office');
        $offer = new Offer();

        if ($request->isPost()) {
            $body = $request->getBody();
            echo "<pre>";
            var_dump($_FILES);
            var_dump($request->getBody());
            echo "</pre>";

            // Retrieve the offer type
            $offer_type = OfferType::findOne(['type' => $request->getBody()['type']]);

            // Create the address
            $address = new Address();
            $address->number = intval($body['address-number']);
            $address->street = $body['address-street'];
            $address->postal_code = $body['address-postal-code'];
            $address->city = $body['address-city'];
            $address->save();

            // Get category
            $category = $body['category'];

            // Create the offer
            $offer->loadData($request->getBody());
            $offer->offline_date = date('Y-m-d');
            $offer->last_online_date = null;
            $offer->category = $category;
            $offer->professional_id = Application::$app->user->account_id;
            $offer->offer_type_id = $offer_type->id;
            $offer->address_id = $address->id;

            if ($offer->validate()) {
                echo "Offer is valid";
            }

            if ($offer->save()) {
                echo "Offer created successfully";
            }

            // Add tags to the offer
            foreach ($body['tags'] as $tag) {
                $tag = strtolower($tag);
                $tagModel = OfferTag::findOne(['name' => $tag]);
                $offer->addTag($tagModel->id);
            }

            // Creation of complementary informations
            if ($category === 'visit') {
                $visit = new VisitOffer();
                $visit->offer_id = $offer->id;
                $visit->duration = $body['visit-duration'];
                $visit->guide = array_key_exists('visit-guide', $body) ? VisitOffer::GUIDE : VisitOffer::NO_GUIDE;

                // TODO - change to fields values
                $period = new OfferPeriod();
                $period->start_date_date = date('Y-m-d');
                $period->end_date = date('Y-m-d');
                $period->save();

                $visit->period_id = $period->id;
                $visit->save();

            } elseif ($category === 'activity') {
                $activity = new ActivityOffer();
                $activity->offer_id = $offer->id;
                $activity->duration = intval($body['activity-duration']);
                $activity->required_age = intval($body['activity-age']);
                $activity->price = intval($body['activity-price']);
                $activity->save();
            } elseif ($category === 'restaurant') {
                $restaurant = new RestaurantOffer();
                $restaurant->url_image_carte = Application::$app->storage->saveFile('restaurant-image', 'offers/restaurant');
                $restaurant->minimum_price = intval($body['restaurant-min-price']);
                $restaurant->maximum_price = intval($body['restaurant-max-price']);
                $restaurant->save();
            } elseif ($category === 'show') {
                $show = new ShowOffer();
                $show->offer_id = $offer->id;
                $show->duration = intval($body['show-duration']);
                $show->capacity = intval($body['show-capacity']);
            } elseif ($category === 'attraction-parc') {

            }

            // Save photos
            $files = Application::$app->storage->saveFiles( 'photos', 'offers');
            foreach ($files as $file) {
                $offer->addPhoto($file);
            }

            exit;
        }

        return $this->render('offers/create', [
            'model' => $offer,
        ]);
    }

    public function detail(Request $request, Response $response, $routeparams)
    {
        $id = $routeparams['pk'];
        $offerData = [];
        $offer = Offer::findOne(['id' => $id]);
        $images = OfferPhoto::find(['offer_id' => $id]) ?? NULL;//get the first image of the offer for the preview
        $url_images = [];
        foreach ($images as $image) {
            array_push($url_images, $image->url_photo);
        }
        $professional = ProfessionalUser::findOne(['user_id' => $offer->professional_id])->denomination ?? NULL;//get the name of the professional who posted the offer

        $offerData = [
            'url_images' => $url_images,
            'date' => $offer->last_online_date,
            'title' => $offer->title,
            'author' => $professional
        ];
        return $this->render('offers/detail', [
            'pk' => $routeparams['pk'],
            "offerData" => $offerData
        ]);
    }
}