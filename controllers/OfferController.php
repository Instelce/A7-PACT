<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\exceptions\NotFoundException;
use app\core\middlewares\AuthMiddleware;
use app\core\middlewares\BackOfficeMiddleware;
use app\core\Request;
use app\core\Response;
use app\core\Utils;
use app\models\Address;
use app\models\Meal;
use app\models\offer\ActivityOffer;
use app\models\offer\AttractionParkOffer;
use app\models\offer\Offer;
use app\models\offer\OfferPeriod;
use app\models\offer\OfferPhoto;
use app\models\offer\OfferTag;
use app\models\offer\OfferType;
use app\models\offer\RestaurantOffer;
use app\models\offer\schedule\OfferSchedule;
use app\models\offer\ShowOffer;
use app\models\offer\VisitOffer;
use app\models\user\professional\ProfessionalUser;
use app\models\VisitLanguage;
use DateInterval;
use DateTime;

class OfferController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['create']));
        $this->registerMiddleware(new BackOfficeMiddleware(['create']));
    }

    public function create(Request $request, Response $response)
    {
        $this->setLayout('back-office');
        $offer = new Offer();

        if ($request->isPost()) {
            $body = $request->getBody();
            //            echo "<pre>";
//            var_dump($_FILES);
//            var_dump($request->getBody());
//            echo "</pre>";

            // Retrieve the offer type
            $offer_type = OfferType::findOne(['type' => $request->getBody()['type']]);

            // Create the address
            $address = new Address();
            $address->number = intval($body['address-number']);
            $address->street = $body['address-street'];
            $address->postal_code = $body['address-postal-code'];
            $address->city = $body['address-city'];
            $address->latitude = $body['address-latitude'];
            $address->longitude = $body['address-longitude'];
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

            // Offer minimum price
            if (array_key_exists('offer-minimum-price', $body) && $category !== 'restaurant') {
                $offer->minimum_price = intval($body['offer-minimum-price']);
            }

            $offer->save();

            // Add tags to the offer
            if (array_key_exists('tags', $body)) {
                foreach ($body['tags'] as $tag) {
                    $tag = strtolower($tag);
                    $tagModel = OfferTag::findOne(['name' => $tag]);
                    $offer->addTag($tagModel->id);
                }
            }

            // Save the offer option
            if (array_key_exists('option', $body) && $body['option'] !== 'no') {
                $offer->addOption($body['option'], $body['option-launch-date'], $body['option-duration']);
            }

            // Creation of complementary informations
            if ($category === 'visit') {
                $visit = new VisitOffer();
                $visit->offer_id = $offer->id;
                $visit->duration = Utils::convertHourToFloat($body['visit-duration']);
                $visit->guide = array_key_exists('visit-guide', $body) ? 1 : 0;

                // Create the period
                if (array_key_exists('period-start', $body)) {
                    $period = new OfferPeriod();
                    $period->start_date = $body['period-start'];
                    $period->end_date = $body['period-end'];
                    $period->save();

                    $visit->period_id = $period->id;
                }

                $visit->save();
            } elseif ($category === 'activity') {
                $activity = new ActivityOffer();
                $activity->offer_id = $offer->id;
                $activity->duration = Utils::convertHourToFloat($body['activity-duration']);
                $activity->required_age = intval($body['activity-age']);
                $activity->save();
            } elseif ($category === 'restaurant') {
                $restaurant = new RestaurantOffer();
                $restaurant->offer_id = $offer->id;
                $restaurant->url_image_carte = Application::$app->storage->saveFile('restaurant-image', 'offers/restaurant');
                $restaurant->range_price = intval($body['restaurant-range-price']);
                $restaurant->save();

//                if(true){ // condition : il clique sur "ajouter un nouveau repas"
//                    $meal = new Meal();
//                    $meal->name = $body['meal-name'];
//                    $meal->price = intval($body['meal-price']);
//                    $meal->save();
//                    $restaurant->addMeal($meal);
//                }

            } elseif ($category === 'show') {
                $show = new ShowOffer();
                $show->offer_id = $offer->id;
                $show->duration = Utils::convertHourToFloat($body['show-duration']);
                $show->capacity = intval($body['show-capacity']);

                // Create the period
                if (array_key_exists('period-start', $body)) {
                    $period = new OfferPeriod();
                    $period->start_date = $body['period-start'];
                    $period->end_date = $body['period-end'];
                    $period->save();

                    $show->period_id = $period->id;
                }

                $show->save();
            } elseif ($category === 'attraction-parc') {
                $attraction = new AttractionParkOffer();
                $attraction->offer_id = $offer->id;
                $attraction->required_age = intval($body['attraction-min-age']);
                $attraction->url_image_park_map = Application::$app->storage->saveFile('attraction-parc-map', 'offers/attraction-parc');
                $attraction->save();
            }

            // Save schedules for restaurant, activity and attraction park offer
            foreach ($body['schedules'] as $dayIndex => $scheduleFields) {
                if ($scheduleFields['open'] !== '' && $scheduleFields['close'] !== '') {
                    $schedule = new OfferSchedule();
                    $schedule->day = $dayIndex + 1;
                    $schedule->opening_hours = $scheduleFields['open'];
                    $schedule->closing_hours = $scheduleFields['close'];
                    $schedule->save();

                    if ($category === 'restaurant') {
                        $restaurant->addSchedule($schedule->id);
                    } else if ($category === 'activity') {
                        $activity->addSchedule($schedule->id);
                    } else if ($category === 'attraction-park') {
                        $attraction->addSchedule($schedule->id);
                    }
                }
            }

            // Save photos
            $files = Application::$app->storage->saveFiles('photos', 'offers');
            foreach ($files as $file) {
                $offer->addPhoto($file);
            }

            // TODO - validate all fields
            // if all fields are valid redirect to the payment page

            return $response->redirect('/offres/' . $offer->id . '/payment');
        }


        return $this->render('offers/create', [
            'model' => $offer,
        ]);
    }

    public function payment(Request $request, Response $response, $routeParams)
    {
        $this->setLayout('back-office');
        $offer = Offer::findOne(['id' => $routeParams['pk']]);

        if (!$offer) {
            throw new NotFoundException();
        }

        return $this->render('offers/payment', [
            'offer' => $offer
        ]);
    }

    public function detail(Request $request, Response $response, $routeparams)
    {
        if (Application::$app->isAuthenticated()) {
            $this->setLayout('back-office');
        }

        $id = $routeparams['pk'];
        $offerData = [];
        $offer = Offer::findOne(['id' => $id]);
        $location = Address::findOne(['id' => $offer->address_id])->city;
        $address = Address::findOne(['id' => $offer->address_id]);

        $tagsName = [];
        $tagsId = $offer->tags();
        foreach ($tagsId as $tag) {
            $tagsName[] = $tag->name;
        }

        $prestationsIncluses = "A";
        $prestationsNonIncluses = "B";
        $accessibilite = "C";

        $languages = VisitLanguage::findOne(['offer_id' => $id])->language;
        $formattedAddress = $address->number . ' ' . $address->street . ', ' . $address->postal_code . ' ' . $address->city;
        $images = OfferPhoto::find(['offer_id' => $id]) ?? NULL;//get the first image of the offer for the preview
        $url_images = [];
        foreach ($images as $image) {
            array_push($url_images, $image->url_photo);
        }

        var_dump(date('N'));

        $closingHour = OfferSchedule::findOne(['id' => $id])->closing_hours;

        if ($closingHour === 'fermé') {
            $status = "Fermé";
        } else {
            $closingTime = new DateTime($closingHour);

            $currentTime = new DateTime();

            if ($closingTime <= $currentTime) {
                $status = "Fermé";
            } elseif ($closingTime <= (clone $currentTime)->add(new DateInterval('PT30M'))) {
                $status = "Ferme bientôt";
            } else {
                $status = "Ouvert";
            }
        }

        $professional = ProfessionalUser::findOne(['user_id' => $offer->professional_id])->denomination ?? NULL;//get the name of the professional who posted the offer

        $type = NULL;
        $duration = NULL;
        $required_age = NULL;
        $price = NULL;
        $range_price = NULL;

        switch ($offer->category) {
            case 'restaurant':
                $type = "Restaurant";
                $range_price = RestaurantOffer::findOne(['offer_id' => $id])->range_price;
                $carte_restaurant = RestaurantOffer::findOne(['offer_id' => $id])->url_image_carte;
                break;
            case 'activity':
                $type = "Activité";
                $duration = ActivityOffer::findOne(['offer_id' => $id])->duration ?? NULL;
                $required_age = ActivityOffer::findOne(['offer_id' => $id])->required_age ?? NULL;
                $price = ActivityOffer::findOne(['offer_id' => $id])->price ?? NULL;
                break;
            case 'show':
                $type = "Spectacle";
                $duration = ShowOffer::findOne(['offer_id' => $id])->duration ?? NULL;
                break;
            case 'visit':
                $type = "Visite";
                $duration = VisitOffer::findOne(['offer_id' => $id])->duration ?? NULL;
                break;
            case 'attraction_park':
                $type = "Parc d'attraction";
                $required_age = AttractionParkOffer::findOne(['offer_id' => $id])->required_age ?? NULL;
                $carte_park = AttractionParkOffer::findOne(['offer_id' => $id])->url_image_park_map;
                break;
        }

        $offerData = [
            'url_images' => $url_images,
            'date' => $offer->last_online_date,
            'title' => $offer->title,
            'author' => $professional,
            'category' => $type,
            'location' => $location,
            'duration' => $duration,
            'required_age' => $required_age,
            'summary' => $offer->summary,
            'website' => $offer->website,
            'address' => $formattedAddress,
            'price' => $price,
            'status' => $status,
            'phone_number' => $offer->phone_number,
            'description' => $offer->description,
            'tags' => $tagsName,
            'languages' => $languages,
            'range_price' => $range_price,
            'prestationsIncluses' => $prestationsIncluses,
            'prestationsNonIncluses' => $prestationsNonIncluses,
            'accessibilite' => $accessibilite,
            'carteRestaurant' => $carte_restaurant,
            'cartePark' => $carte_park
        ];

        return $this->render('offers/detail', [
            'pk' => $routeparams['pk'],
            'offerData' => $offerData
        ]);
    }

    public function update(Request $request, Response $response, $routeparams)
    {
        $this->setLayout('back-office');
        $offer = Offer::findOne(['id' => $routeparams['pk']]);
        $address = Address::findOne(['id' => $offer->address_id]);
        $specificData = $offer->specificData();
        $images = OfferPhoto::find(['offer_id' => $offer->id]) ?? NULL;
        $tags = $offer->tags();

        $offerData = [
            'title' => $offer->title,
            'category' => $offer->category,
            'offline' => $offer->offline,
            'images' => $images,
            'tags' => array_map(fn($tag) => $tag->name, $tags),
        ];

        if ($request->isPost()) {
            $body = $request->getBody();

            // Update the address
            $address = Address::findOne(['id' => $offer->address_id]);
            $address->loadData($request->getBody());
            $address->update();

            // Update the offer
            $offer->loadData($request->getBody());
            $offer->professional_id = Application::$app->user->account_id;
            $offer->address_id = $address->id;
            if (array_key_exists("online", array: $body)) {
                $offer->offline = 0;
                $offer->last_online_date = date('Y-m-d');
            } else {
                $offer->offline_date = date('Y-m-d');
                $offer->offline = 1;

            }

            // Offer minimum price
            $category = $offer->category;
            if (array_key_exists('offer-minimum-price', $body) && $category !== 'restaurant') {
                $offer->minimum_price = intval($body['offer-minimum-price']);
            }

            if ($offer->update()) {
                echo "Offer updated successfully";
            }
            // Add tags to the offer
            if (array_key_exists('tags', $body)) {
                foreach ($body['tags'] as $tag) {
                    $tag = strtolower($tag);
                    $tagModel = OfferTag::findOne(['name' => $tag]);
                    $offer->addTag($tagModel->id);
                }
            }

            // Update of complementary informations
            if ($category === 'visit') {
                $visit = VisitOffer::findOne(['offer_id' => $offer->id]);
                $visit->offer_id = $offer->id;
                $visit->duration = Utils::convertHourToFloat($body['duration']);
                $visit->guide = array_key_exists('visit-guide', $body) ? 1 : 0;

                // Update the period
                if (array_key_exists('period-start', $body) && $body['period-start'] !== '') {
                    $period = OfferPeriod::findOne(['id' => $offer->id]);
                    $period->start_date = $body['period-start'];
                    $period->end_date = $body['period-end'];
                    $period->update();

                    $visit->period_id = $period->id;
                }

                $visit->update();
            } elseif ($category === 'activity') {
                $activity = ActivityOffer::findOne(['offer_id' => $offer->id]);
                $activity->offer_id = $offer->id;
                $activity->duration = Utils::convertHourToFloat($body['duration']);
                $activity->required_age = intval($body['required_age']);
                $activity->update();
            } elseif ($category === 'restaurant') {
                $restaurant = RestaurantOffer::findOne(['offer_id' => $offer->id]);
                $restaurant->offer_id = $offer->id;
                $restaurant->url_image_carte = Application::$app->storage->saveFile('restaurant-image', 'offers/restaurant');
                $restaurant->range_price = intval($body['restaurant-range-price']);
                $restaurant->update();
            } elseif ($category === 'show') {
                $show = ShowOffer::findOne(['offer_id' => $offer->id]);
                $show->offer_id = $offer->id;
                $show->duration = Utils::convertHourToFloat($body['duration']);
                $show->capacity = intval($body['capacity']);

                // Update the period
                if (array_key_exists('period-start', $body) && $body['period-start'] !== '') {
                    $period = OfferPeriod::findOne(['offer_id' => $offer->id]);
                    $period->start_date = $body['period-start'];
                    $period->end_date = $body['period-end'];
                    $period->update();

                    $show->period_id = $period->id;
                }

                $show->update();
            } elseif ($category === 'attraction-parc') {
                $attraction = AttractionParkOffer::findOne(['offer_id' => $offer->id]);
                $attraction->offer_id = $offer->id;
                $attraction->required_age = intval($body['attraction-min-age']);
                $attraction->url_image_park_map = Application::$app->storage->saveFile('attraction-parc-map', 'offers/attraction-parc');
                $attraction->update();
            }

            // Delete images
            foreach ($body['deleted-photos'] as $imageId) {
                $image = OfferPhoto::findOneByPk($imageId);
                $image->destroy();
            }

            // Save photos
            $files = Application::$app->storage->saveFiles('photos', 'offers');
            foreach ($files as $file) {
                $offer->addPhoto($file);
            }

            // TODO - validate all fields
            // if all fields are valid redirect to the payment page

            Application::$app->session->setFlash('success', 'Offre modifiée avec succès');

            return $response->redirect('/offres/' . $offer->id . '/modification');
        }

        return $this->render('offers/update', [
            'pk' => $routeparams['pk'],
            'offer' => $offerData,
            'model' => $offer,
            'address' => $address,
            'specificData' => $specificData
        ]);
    }
}