<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\exceptions\NotFoundException;
use app\core\middlewares\AuthMiddleware;
use app\core\Request;
use app\core\Response;
use app\core\Utils;
use app\forms\PaymentForm;
use app\middlewares\BackOfficeMiddleware;
use app\middlewares\OwnOfferMiddleware;
use app\models\account\UserAccount;
use app\models\Address;
use app\models\offer\ActivityOffer;
use app\models\offer\AttractionParkOffer;
use app\models\offer\Offer;
use app\models\offer\OfferPeriod;
use app\models\offer\OfferPhoto;
use app\models\offer\OfferStatusHistory;
use app\models\offer\OfferTag;
use app\models\offer\OfferType;
use app\models\offer\RestaurantOffer;
use app\models\offer\schedule\LinkSchedule;
use app\models\offer\schedule\OfferSchedule;
use app\models\offer\ShowOffer;
use app\models\offer\VisitOffer;
use app\models\opinion\Opinion;
use app\models\payment\CbMeanOfPayment;
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
        $this->registerMiddleware(new OwnOfferMiddleware(['update']));
    }

    public function create(Request $request, Response $response)
    {
        $this->setLayout('back-office');
        $offer = new Offer();

        if ($request->isPost()) {
            $body = $request->getBody();

            // Retrieve the offer type
            $type = $body['type'] ?? 'gratuite';
            $offer_type = OfferType::findOne(['type' => $type]);

            // Create the address
            $address = new Address();
            $address->number = intval($body['address-number']);
            $address->street = $body['address-street'];
            $address->postal_code = $body['address-postal-code'];
            $address->city = $body['address-city'];
            if (array_key_exists('address-latitude', $body) && $body['address-latitude'] !== '') {
                $address->latitude = $body['address-latitude'];
                $address->longitude = $body['address-longitude'];
            }
            $address->save();

            // Get category
            $category = $body['category'];

            // Create the offer
            $offer->loadData($request->getBody());
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
                $offer->addSubscription($body['option'], $body['option-launch-date'], $body['option-duration']);
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
                    $period->offer_id = $offer->id;
                    $period->save();
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
                    $period->offer_id = $offer->id;
                    $period->save();
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
            // if all fields are valid redirect to the payment page or on dashboard

            if (Application::$app->user->isPrivateProfessional()) {
                return $response->redirect('/offres/' . $offer->id . '/payment');
            } else {
                return $request->redirect('/dashboard');
            }
        }

        return $this->render('offers/create', [
            'model' => $offer,
        ]);
    }

    public function payment(Request $request, Response $response, $routeParams)
    {
        $this->setLayout('back-office');
        $offer = Offer::findOneByPk($routeParams['pk']);
        $professional = UserAccount::findOneByPk($offer->professional_id);
        $address = Address::findOneByPk($professional->address_id);
        $payment = new PaymentForm($professional->specific()->specific()->payment_id);

        if (!$offer) {
            throw new NotFoundException();
        }

        return $this->render('offers/payment', [
            'offer' => $offer,
            'address' => $address,
            'professional' => $professional,
            'payment' => $payment
        ]);
    }

    public function detail(Request $request, Response $response, $routeparams)
    {
        $id = $routeparams['pk'];
        $offerData = [];
        $offer = Offer::findOneByPk($id);
        $location = Address::findOne(['id' => $offer->address_id])->city;
        $address = Address::findOne(['id' => $offer->address_id]);

        $tagsName = [];
        $tagsId = $offer->tags();
        foreach ($tagsId as $tag) {
            $tagsName[] = $tag->name;
        }

        $prestationsIncluses = "";
        $prestationsNonIncluses = "";
        $accessibilite = "";

        $languages = VisitLanguage::findOne(['offer_id' => $id])->language;
        $formattedAddress = $address->number . ' ' . $address->street . ', ' . $address->postal_code . ' ' . $address->city;
        $images = OfferPhoto::find(['offer_id' => $id]) ?? NULL;//get the first image of the offer for the preview
        $url_images = [];
        foreach ($images as $image) {
            array_push($url_images, $image->url_photo);
        }

        $openingHours = $offer->schedule();
        $dayOfWeek = strtolower((new DateTime())->format('N'));
        foreach($openingHours as $openingHour){
            if($openingHour->day==$dayOfWeek){
                $todayHour = $openingHour;
            }
        }

        $closingHour = $todayHour->closing_hours;
        $openingHour = $todayHour->opening_hours;

        if ($todayHour){
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
        } else {$status = NULL;}




        $professional = ProfessionalUser::findOne(['user_id' => $offer->professional_id])->denomination ?? NULL;//get the name of the professional who posted the offer

        $type = NULL;
        $duration = NULL;
        $required_age = NULL;
        $price = NULL;
        $range_price = NULL;

        $servicesIncluded = NULL;
        $servicesNonIncluded = NULL;
        $accessibility = NULL;
        $mealsIncluded = NULL;

        switch ($offer->category) {
            case 'restaurant':
                $type = "Restaurant";
                $range_price = RestaurantOffer::findOne(['offer_id' => $id])->range_price;
                $carte_restaurant = RestaurantOffer::findOne(['offer_id' => $id])->url_image_carte;
                $price = $range_price === 1 ? "Dès €" : ($range_price === 2 ? "Dès €€" : "Dès €€€");
                $mealsIncluded = "Petit dej";
                break;
            case 'activity':
                $type = "Activité";
                $duration = ActivityOffer::findOne(['offer_id' => $id])->duration ?? NULL;
                $required_age = ActivityOffer::findOne(['offer_id' => $id])->required_age ?? NULL;
                $price = $offer->minimum_price !== null ? "Dès " . $offer->minimum_price . "€ /Pers." : "Gratuit";
                $servicesIncluded = "Le vélo";
                $servicesNonIncluded = "La selle";
                $accessibility = "Pour tout le monde";
                break;
            case 'show':
                $type = "Spectacle";
                $duration = ShowOffer::findOne(['offer_id' => $id])->duration ?? NULL;
                $price = $offer->minimum_price !== null ? "Dès " . $offer->minimum_price . "€ /Pers." : "Gratuit";
                break;
            case 'visit':
                $type = "Visite";
                $duration = VisitOffer::findOne(['offer_id' => $id])->duration ?? NULL;
                $price = $offer->minimum_price !== null ? "Dès " . $offer->minimum_price . "€ /Pers." : "Gratuit";
                break;
            case 'attraction_park':
                $type = "Parc d'attraction";
                $price = $offer->minimum_price !== null ? "Dès " . $offer->minimum_price . "€ /Pers." : "Gratuit";
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
            'carteRestaurant' => $carte_restaurant,
            'cartePark' => $carte_park,
            'professionalId' => $offer->professional_id,
            'rating' => $offer->rating(),
            'servicesIncluded' => $servicesIncluded,
            'servicesNonIncluded' => $servicesNonIncluded,
            'accessibility' => $accessibility,
            'mealsIncluded' => $mealsIncluded,
            'openingHours' => $openingHours,
        ];

        // Opinion creation
        $opinion = new Opinion();
        $opinionSubmitted = false;

        if ($request->isPost() && $request->formName() === 'create-opinion') {
            $opinion->loadData($request->getBody());

            if ($opinion->validate()) {
                $opinion->account_id = Application::$app->user->account_id;
                $opinion->offer_id = $id;
                $opinion->save();

                // TODO - update rating column on offer
                $offer->rating = $offer->rating();
                $offer->update();

                // Save opinion photos
                $files = Application::$app->storage->saveFiles('opinion-photos', 'opinions');

                foreach ($files as $file) {
                    $opinion->addPhoto($file);
                }

                $opinionSubmitted = false;
            } else {
                $opinionSubmitted = true;
            }
        }

        // Retrieve the user opinion
        if (Application::$app->isAuthenticated()) {
            $userOpinion = Opinion::findOne(['account_id' => Application::$app->user->account_id, 'offer_id' => $id]);
        } else {
            $userOpinion = false;
        }

        // Delete user opinion
        if ($request->isPost() && $request->formName() === 'delete-opinion') {
            $opinion = Opinion::findOneByPk($request->getBody()['opinion_id']);
            $opinion->destroy();
            $userOpinion = false;

            $offer->rating = $offer->rating();
            $offer->update();
        }

        return $this->render('offers/detail', [
            'pk' => $routeparams['pk'],
            'offer' => $offer,
            'offerData' => $offerData,
            'opinion' => $opinion,
            'opinionSubmitted' => $opinionSubmitted,
            'userOpinion' => $userOpinion
        ]);
    }

    public function update(Request $request, Response $response, $routeparams)
    {
        $this->setLayout('back-office');

        /** @var Offer $offer */
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

            // Switching to online
            if (array_key_exists("online", array: $body) && $offer->offline === 1) {
                $offer->offline = 0;

                // Add history line
                $history = new OfferStatusHistory();
                $history->offer_id = $offer->id;
                $history->switch_to = "online";
                $history->save();
            }
            // Switching to offline
            else if ($offer->offline === 0) {
                $offer->offline = 1;

                // Add history line
                $history = new OfferStatusHistory();
                $history->offer_id = $offer->id;
                $history->switch_to = "offline";
                $history->save();
            }

            // Offer minimum price
            $category = $offer->category;
            if (array_key_exists('offer-minimum-price', $body) && $category !== 'restaurant') {
                $offer->minimum_price = intval($body['offer-minimum-price']);
            }

            $offer->update();

            // Add tags to the offer
            if (array_key_exists('tags', $body)) {
                foreach ($body['tags'] as $tagName) {

                    $tagName = strtolower($tagName);
                    $tagModel = OfferTag::findOne(['name' => $tagName]);

                    // Check if the tag
                    if (!$offer->hasTag($tagName)) {
                        $offer->addTag($tagModel->id);
                    }
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
                    $period->offer_id = $offer->id;
                    $period->update();


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
                    $period->offer_id = $offer->id;
                    $period->update();


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