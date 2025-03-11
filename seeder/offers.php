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

use app\models\opinion\Opinion;
use app\models\user\MemberUser;



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
        "concerts" => "show",
        "sorties-nature" => "activity",
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
            $capcacities = [100, 120, 130, 140, 150, 160, 170, 180, 190, 200, 250, 300];
            $show->capacity = $capcacities[array_rand($capcacities)];
            $show->save();
        } else {
        }
    }
}


// ---------------------------------------------------------------------- //
// Generate opinions
// ---------------------------------------------------------------------- //

/** @var Offer[] $offers */
$offers = Offer::all();
/** @var MemberUser[] $members */
$members = MemberUser::all();

$contexts = ["affaires", "couple", "famille", "amis", "solo"];
$reviews = [
    // Restaurants
    "restaurant" => [
        [
            "title" => "Un dîner inoubliable",
            "content" => "Une cuisine raffinée et un service impeccable. Mention spéciale pour le chef qui est venu nous saluer à la fin du repas.",
            "rating" => 5,
        ],
        [
            "title" => "Correct mais sans plus",
            "content" => "Les plats étaient bons mais manquaient un peu de saveur. Le service était rapide mais pas très chaleureux.",
            "rating" => 3,
        ],
        [
            "title" => "Un cadre charmant",
            "content" => "Ce restaurant offre une vue magnifique et une atmosphère paisible. La nourriture était correcte, mais les prix un peu élevés.",
            "rating" => 4,
        ],
        [
            "title" => "Mauvaise expérience",
            "content" => "Service très lent et plats froids à l'arrivée. Une grosse déception pour ce restaurant pourtant bien noté.",
            "rating" => 2,
        ],
        [
            "title" => "Le paradis des gourmets",
            "content" => "Une explosion de saveurs dans chaque bouchée. Le personnel est aux petits soins. Je recommande vivement !",
            "rating" => 5,
        ],
        [
            "title" => "Brunch très réussi",
            "content" => "Les viennoiseries étaient excellentes et les jus fraîchement pressés. Parfait pour un début de journée gourmand.",
            "rating" => 4,
        ],
        [
            "title" => "Service moyen mais bonne cuisine",
            "content" => "Les plats étaient délicieux, mais le personnel semblait débordé et peu réactif. Dommage.",
            "rating" => 3,
        ],
        [
            "title" => "Un vrai délice pour les papilles",
            "content" => "Des plats créatifs et savoureux, servis avec le sourire. Une adresse à ne pas manquer.",
            "rating" => 5,
        ],

        [
            "title" => "Service déplorable",
            "content" => "Le serveur était impoli et nous avons attendu plus d'une heure pour des plats froids et mal assaisonnés. Une véritable déception.",
            "rating" => 1,
            "activity" => "restaurant"
        ],
        [
            "title" => "Une mauvaise surprise",
            "content" => "Les photos en ligne semblaient prometteuses, mais la réalité était bien différente. Plats insipides et ambiance désagréable.",
            "rating" => 2,
            "activity" => "restaurant"
        ]
    ],

    // Visits
    "visit" => [
        [
            "title" => "Une visite fascinante",
            "content" => "Le guide était passionné et les anecdotes étaient captivantes. Une excellente activité culturelle.",
            "rating" => 5,
        ],
        [
            "title" => "Pas à la hauteur des attentes",
            "content" => "Le site est intéressant, mais la visite était trop courte et manquait de détails.",
            "rating" => 3,
        ],
        [
            "title" => "Un lieu à voir absolument",
            "content" => "Magnifique et chargé d'histoire. Je recommande de prendre le temps d'explorer chaque recoin.",
            "rating" => 5,
        ],
        [
            "title" => "Un endroit bien préservé",
            "content" => "Le site est bien conservé et l'expérience était immersive. Prévoir des chaussures confortables pour la marche.",
            "rating" => 4,
        ],
        [
            "title" => "Trop de monde",
            "content" => "Difficile de profiter pleinement de la visite avec autant de monde. Dommage, car le lieu est superbe.",
            "rating" => 3,
        ],
        [
            "title" => "Un joyau caché",
            "content" => "Un endroit peu connu mais absolument magnifique. Calme et sérénité garanties.",
            "rating" => 5,
        ],
        [
            "title" => "Très instructif",
            "content" => "Nous avons beaucoup appris lors de cette visite. Les enfants ont aussi adoré les activités proposées.",
            "rating" => 4,
        ],
        [
            "title" => "Décevant",
            "content" => "Le site semblait intéressant, mais la visite guidée était monotone et peu engageante.",
            "rating" => 2,
        ],

        [
            "title" => "Un lieu surcoté",
            "content" => "Mal indiqué, mal entretenu, et rien de spécial à voir. Nous avons regretté d’avoir perdu notre temps ici.",
            "rating" => 2,
            "activity" => "visit"
        ],
        [
            "title" => "Très décevant",
            "content" => "Le site est petit et sans intérêt. La visite guidée manquait d'informations et le guide semblait pressé d'en finir.",
            "rating" => 1,
            "activity" => "visit"
        ],
    ],

    // Attraction Parks
    "attraction_park" => [
        [
            "title" => "Une journée riche en émotions",
            "content" => "Les attractions sont variées et adaptées à tous les âges. Une excellente journée en famille !",
            "rating" => 5,
        ],
        [
            "title" => "Trop d'attente",
            "content" => "Les attractions étaient bien, mais les files d'attente interminables ont gâché notre journée.",
            "rating" => 3,
        ],
        [
            "title" => "Un parc exceptionnel",
            "content" => "Les décors sont magnifiques et les sensations fortes au rendez-vous. Nous avons adoré !",
            "rating" => 5,
        ],
        [
            "title" => "Une expérience moyenne",
            "content" => "Les attractions sont bien, mais les prix sont beaucoup trop élevés, surtout pour la nourriture.",
            "rating" => 3,
        ],
        [
            "title" => "Parfait pour les enfants",
            "content" => "Un parc adapté aux plus petits avec beaucoup de jeux et des espaces sécurisés. Les enfants étaient ravis.",
            "rating" => 4,
        ],
        [
            "title" => "Une ambiance féérique",
            "content" => "Tout était parfait, des attractions aux spectacles en passant par les décors. Un vrai moment de magie.",
            "rating" => 5,
        ],
        [
            "title" => "Bien mais cher",
            "content" => "Le parc est magnifique, mais le prix d'entrée et des extras est exorbitant. Préparez votre budget.",
            "rating" => 3,
        ],
        [
            "title" => "Une journée mouvementée",
            "content" => "Les attractions sont géniales, mais l'organisation pour gérer les flux de visiteurs pourrait être améliorée.",
            "rating" => 4,
        ],

        [
            "title" => "Une expérience catastrophique",
            "content" => "Les manèges étaient souvent en panne, et le personnel était désagréable. À éviter absolument.",
            "rating" => 1,
            "activity" => "attraction_park"
        ],
        [
            "title" => "Trop cher et trop de monde",
            "content" => "Impossible de profiter des attractions avec des heures d’attente. Une journée gâchée.",
            "rating" => 2,
            "activity" => "attraction_park"
        ],
    ],

    // Shows ---------------------------------------------------------------------------------------
    "show" => [
        [
            "title" => "Un spectacle époustouflant",
            "content" => "Les artistes étaient incroyables, et les effets visuels magnifiques. Une soirée mémorable.",
            "rating" => 5,
        ],
        [
            "title" => "Pas mal mais trop court",
            "content" => "Le spectacle était bien, mais la durée était vraiment trop courte pour le prix payé.",
            "rating" => 3,
        ],
        [
            "title" => "Une ambiance magique",
            "content" => "Un show captivant du début à la fin. L'éclairage et la musique étaient particulièrement réussis.",
            "rating" => 5,
        ],
        [
            "title" => "Des longueurs",
            "content" => "Certains passages du spectacle étaient très bien, mais d'autres traînaient en longueur. Moyen dans l'ensemble.",
            "rating" => 3,
        ],
        [
            "title" => "Un moment d'émotion",
            "content" => "Un spectacle qui nous a fait vibrer et même pleurer. Bravo aux artistes pour leur talent !",
            "rating" => 5,
        ],
        [
            "title" => "Manque d'énergie",
            "content" => "Le spectacle aurait pu être plus dynamique. Les performances étaient correctes, mais rien d'extraordinaire.",
            "rating" => 3,
        ],
        [
            "title" => "Une soirée réussie",
            "content" => "Un excellent spectacle avec des performances impressionnantes. Nous avons passé un très bon moment.",
            "rating" => 4,
        ],
        [
            "title" => "Des artistes talentueux",
            "content" => "Le spectacle était magnifique, et la qualité des prestations était exceptionnelle. Je recommande sans hésiter.",
            "rating" => 5,
        ],

        [
            "title" => "Spectacle ennuyant",
            "content" => "Le rythme était beaucoup trop lent, et les performances étaient loin d’être impressionnantes. Je me suis presque endormi.",
            "rating" => 2,
            "activity" => "show"
        ],
        [
            "title" => "Une grosse perte de temps",
            "content" => "Le show manquait de cohérence et de professionnalisme. Les acteurs semblaient peu préparés.",
            "rating" => 1,
            "activity" => "show"
        ],
    ],

    // Activities
    "activity" => [

        [
            "title" => "Une expérience unique",
            "content" => "Une activité originale et bien organisée. Parfait pour découvrir quelque chose de nouveau.",
            "rating" => 5,
        ],
        [
            "title" => "Pas à la hauteur",
            "content" => "L'organisation était décevante, et l'activité n'était pas aussi intéressante que prévu.",
            "rating" => 2,
        ],
        [
            "title" => "Un moment de détente",
            "content" => "Une activité très relaxante dans un cadre agréable. Idéal pour déconnecter un moment.",
            "rating" => 4,
        ],
        [
            "title" => "Super pour les groupes",
            "content" => "Une activité qui nous a permis de passer un excellent moment entre amis. Nous avons bien rigolé !",
            "rating" => 5,
        ],
        [
            "title" => "Activité basique",
            "content" => "C'était correct, mais sans grande originalité. Je m'attendais à mieux pour le prix.",
            "rating" => 3,
        ],
        [
            "title" => "Très amusant",
            "content" => "Une activité dynamique et ludique. Les enfants ont adoré et nous aussi !",
            "rating" => 5,
        ],
        [
            "title" => "Manque de professionnalisme",
            "content" => "L'activité aurait pu être agréable, mais l'organisation laissait à désirer. Un peu décevant.",
            "rating" => 2,
        ],
        [
            "title" => "Génial du début à la fin",
            "content" => "Tout était parfait : l'organisation, l'ambiance, et le plaisir partagé. Un vrai coup de cœur !",
            "rating" => 5,
        ],

        [
            "title" => "Une activité mal organisée",
            "content" => "Le personnel n’était pas compétent, et l’équipement fourni était en mauvais état. Une très mauvaise expérience.",
            "rating" => 1,
            "activity" => "activity"
        ],
        [
            "title" => "Très ennuyeux",
            "content" => "L’activité manquait d’originalité et était beaucoup trop chère pour ce que c’est. Je ne recommande pas.",
            "rating" => 2,
            "activity" => "activity"
        ]
    ],
];

foreach ($offers as $offer) {
    $count = rand(6, 20);
    $account_ids = [];

    for ($k = 0; $k < $count; $k++) {
        $member_id = $members[array_rand($members)]->user_id;
        if (in_array($member_id, $account_ids)) {
            continue;
        }

        $review = $reviews[$offer->category][array_rand($reviews[$offer->category])];
        $opinion = new Opinion();
        $opinion->rating = $review['rating'];
        $opinion->title = $review['title'];
        $opinion->comment = $review['content'];
        $opinion->offer_id = $offer->id;
        $opinion->account_id = $member_id;
        $opinion->read = false;
        $opinion->blacklisted = false;
        $opinion->visit_context = $contexts[array_rand($contexts)];
        $opinion->visit_date = date('Y-m-d', rand(strtotime($offer->created_at), strtotime('-7 days')));
        $opinion->created_at = date('Y-m-d H:i:s', rand(strtotime($opinion->visit_date), strtotime('-2 days')));
        $opinion->nb_reports = 0;
        $opinion->save();
        $account_ids[] = $opinion->account_id;

        $offer->rating = $offer->rating();
        $offer->update();
    }

}



?>