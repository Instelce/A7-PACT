<?php

// ---------------------------------------------------------------------------------------------- //
// Connection to the database
// ---------------------------------------------------------------------------------------------- //

use app\core\Application;
use app\models\Address;
use app\models\offer\ActivityOffer;
use app\models\offer\Offer;
use app\models\offer\OfferPhoto;
use app\models\offer\RestaurantOffer;
use app\models\offer\ShowOffer;
use app\models\offer\VisitOffer;
use app\models\user\professional\ProfessionalUser;

require_once __DIR__ . '/../vendor/autoload.php';

// setup env variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$config = [
    'userClass' => Account::class,
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD']
    ]
];

$app = new Application(__DIR__ . '/..', $config);
$db = $app->db;


// ---------------------------------------------------------------------------------------------- //
// Create all offers that is scrapped
// ---------------------------------------------------------------------------------------------- //

/**
 * @return 'activity' | 'attraction_park' | 'restaurant' | 'show' | 'visit'
 */
function getCategoryFromFileName($filename) {
    return match ($filename) {
        "phares" => "visit",
        "parcs-jardins" => "visit",
        "restaurants" => "restaurant",
        "plages" => "activity",
        "jeux-enigmes" => "activity",
        "parcs-animaliers" => "activity",
        "fest-noz" => "show",
        "menhirs-dolmens" => "visit",
    };
}

$files = glob(__DIR__ . "/fetcher/data/*.json");
$professionals = ProfessionalUser::all();

echo "Found " . count($files) . " files to process\n";
echo "Professionals count: " . count($professionals) . "\n";

foreach ($files as $file) {
    echo "Processing $file...\n";
    $json = file_get_contents($file);
    $json = mb_convert_encoding($json, 'UTF-8', 'auto');
    $data = json_decode($json, true);

    foreach ($data as $offerData) {
        echo "\tProcessing offer {$offerData['title']}...\n";
        if (empty($offerData['longitude']) && empty($offerData['latitude'])) {
            echo "\t  Skip offer {$offerData['title']} because of missing location\n";
            continue;
        }

        $category = getCategoryFromFileName(basename($file, ".json"));
        /** @var $professional ProfessionalUser */
        $professional = $professionals[array_rand($professionals)];

        $addressParts = explode("\n", $offerData["location"]);
        $postalCode = explode(" ", end($addressParts))[0];
        $city = explode(" ", end($addressParts))[1] ? explode(" ", end($addressParts))[1] : $addressParts[0];

        $address = new Address();
        $address->longitude = $offerData["longitude"];
        $address->latitude = $offerData["latitude"];
        $address->number = 0;
        $address->street = $addressParts[0];
        $address->city = $city;
        $address->postal_code = substr($postalCode, 0, 5);
        $address->save();

        $offer = new Offer();
        $offer->professional_id = $professional->user_id;

        /* Offer type */
        if ($professional->isPrivate()) {
            if ($category == "restaurant" && rand(0, 3) == 1) {
                $offer->offer_type_id = 2;
            } else {
                $offer->offer_type_id = 1;
            }

            if ($category != "restaurant" && rand(0, 5) == 1) {
                $offer->offer_type_id = 2;
            } else {
                $offer->offer_type_id = 1;
            }
        } else {
            $offer->offer_type_id = 3;
        }

        $offer->title = mb_convert_encoding($offerData["title"], 'UTF-8', 'auto');
        $offer->description = mb_convert_encoding(substr($offerData["description"], 0, 1024), 'UTF-8', 'auto');
        $offer->summary = substr(explode(".", $offer->description)[0], 0, 384);

        if (!empty($offerData['website'])) {
            $offer->website = $offerData['website'];
        }

        $offer->offline = Offer::STATUS_ONLINE;
        $offer->category = $category;
        $offer->address_id = $address->id;
        $offer->save();


        // Load photos
        foreach ($offerData['images'] as $photo) {
            $offerPhoto = new OfferPhoto();
            $offerPhoto->offer_id = $offer->id;
            $offerPhoto->url_photo = $photo;
            $offerPhoto->save();
        }

        // Specific data for each category
        if ($category == "restaurant") {
            $restaurant = new RestaurantOffer();
            $restaurant->offer_id = $offer->id;
            $restaurant->range_price = rand(1, 4);
            $restaurant->url_image_carte = '';
            $restaurant->save();
        } else if ($category == "activity") {
            $activity = new ActivityOffer();
            $activity->offer_id = $offer->id;
            $activity->duration = rand(1, 4);
            $activity->required_age = rand(4, 18);
            $activity->save();
        } else if ($category == "visit") {
            $visit = new VisitOffer();
            $visit->offer_id = $offer->id;
            $visit->duration = rand(1, 4);
            $visit->guide = rand(0, 1);
            $visit->save();
        } else if ($category == "show") {
            $show = new ShowOffer();
            $show->offer_id = $offer->id;
            $show->duration = rand(1, 4);
            $show->capacity = rand(10, 100);
            $show->save();
        } else {
        }
    }
}


?>