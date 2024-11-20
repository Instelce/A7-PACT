<?php

use app\core\Application;
use app\models\account\Account;
use app\models\offer\Offer;
use app\models\offer\OfferPhoto;
use app\models\offer\OfferTag;
use app\models\offer\RestaurantOffer;
use app\models\offer\AttractionParkOffer;
use app\models\offer\ActivityOffer;
use app\models\Meal;
use app\models\offer\schedule\OfferSchedule;


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

$password = password_hash("JuPeNoCs24", PASSWORD_DEFAULT);



$db->pdo->exec("TRUNCATE TABLE address, offer_tag, offer_photo, offer_option, offer, offer_type, offer_period, private_professional, public_professional, professional_user, member_user, administrator_user, user_account, anonymous_account, account, mean_of_payment RESTART IDENTITY CASCADE;");

// ---------------------------------------------------------------------- //
// user adress
// ---------------------------------------------------------------------- //

$db->pdo->exec("INSERT INTO address (id, number, street, city, postal_code, longitude, latitude) VALUES 
                                                                    (11,68, 'Avenue Millies Lacroix', 'Eaubonne', 95600,2.2779189 ,48.992128), 
                                                                    (12,90,'rue de Lille','Asnières-sur-Seine',92600,2.285369,48.914155),
                                                                    (13,44,'rue de la Hulotais','Saint-priest',69800,4.947071,45.698938),
                                                                    (14,47,'boulevard Bryas',' Dammarie-les-lys',77190,	2.634831,48.515451),
                                                                    (15, 1, 'Rue de la Mairie', 'Lannion', 22300, -3.4597, 48.7326)
                                                                    ;");

// ---------------------------------------------------------------------- //
// create offer adress
// ---------------------------------------------------------------------- //

$db->pdo->exec("INSERT INTO address (id, number, street, city, postal_code, longitude, latitude) VALUES 
                                                                    (21, 2, 'Rue des Halles', 'Lannion', 22300, -3.4597,48.7326 ), 
                                                                    (22, 1, 'Parc du Radôme', 'Pleumeur-Bodou', 22560, 	-3.474171, 	48.800755),
                                                                    (23, 1, 'Parking du plan deau', 'Samson-sur-Rance', 22100, -3.4597, 48.7326),
                                                                    (24, 13, 'Rue des Ruees', 'Tréhorenteuc', 56430, 48.00799182324886, -2.2850415831640905),
                                                                    (16, 1, 'Crec’h Kerrio', 'Île-de-Bréhat', 22870, 48.84070603138791, -2.999732772564104),
                                                                    (17, 1, 'La Récré des 3 Curés', 'Les Trois Cures', 29290, 48.47492014209391, -4.526581655177133),
                                                                    (18, 1, 'La Vallée des Saints', 'Carnoët', 22160, 48.84070603138791, -2.999732772564104),
                                                                    (19, 3, 'Rue des potiers','Noyal-Châtillon-sur-Seiche', 35230,48.041895277402126, -1.6674224847189223);");


// ---------------------------------------------------------------------- //
// create users
// ---------------------------------------------------------------------- //
$db->pdo->exec("INSERT INTO account (id) VALUES (1), (2), (3), (4), (5), (6), (7), (8), (9), (10);");
$db->pdo->exec("INSERT INTO user_account (account_id, mail, password, avatar_url, address_id) VALUES 
                                                                     (1, 'rouevictor@gmail.com', '" . $password . "','https://i.pinimg.com/control/564x/a2/a9/fd/a2a9fdfb77c19cc7b5e1749718228945.jpg',11), 
                                                                     (2, 'eliaz.chesnel@outlook.fr', '" . $password . "', 'https://preview.redd.it/4l7yhfrppsh51.jpg?width=640&crop=smart&auto=webp&s=11445a8cd85d7b4e81170491d3f013e5599048ae',12), 
                                                                     (3, 'sergelemytho@gmail.com','" . $password . "','https://media.gqmagazine.fr/photos/5e135c806b02b40008e0d316/1:1/w_1600%2Cc_limit/thumbnail_sergemytho.jpg',13),
                                                                     (4, 'fredlechat@gmail.com', '" . $password . "', 'https://i.chzbgr.com/full/10408722944/hDAD92EF6/ole',14),                                                            
                                                                     (5, 'rance.evasion@gmail.com', '" . $password . "', 'https://fr.web.img5.acsta.net/pictures/16/05/17/12/17/360795.jpg', 15),
                                                                     (6, 'roiduvoyage@gmail.com', '" . $password . "', 'https://cdn.discordapp.com/attachments/1194441121376514099/1298550202579554314/roi_brigand.png?ex=6719f89e&is=6718a71e&hm=b3ad4fe032eb2ed29b6f15aba207cb3132a322b9c69317afa07869d783b13269&',19),
                                                                     (8, 'brehat@gmail.com', '" . $password . "', 'https://png.pngtree.com/png-clipart/20230927/original/pngtree-man-avatar-image-for-profile-png-image_13001882.png',16),
                                                                     (9, 'recree_trois_cures@gmail.com', '" . $password . "', 'https://www.larecredes3cures.com/app/uploads/2024/04/vertika-la-recre-des-3-cures-scaled-910x668-c-center.jpg',17),
                                                                     (10, 'valleedessaints@gmail.com', '" . $password . "', 'https://media.letelegramme.fr/api/v1/images/view/637cf1668f4302361f300639/web_golden_xl/637cf1668f4302361f300639.1',18);");

// ---------------------------------------------------------------------- //
// create means of payment
// ---------------------------------------------------------------------- //

$db->pdo->exec("INSERT INTO mean_of_payment (id) VALUES (1), (2), (3);");
$db->pdo->exec("INSERT INTO cb_mean_of_payment (payment_id, name, card_number, expiration_date, cvv) VALUES (1, 'Fred port', '1548759863254125', '07/25', '123'),(2,'Rance Evasion','4287621589632154','08/29','123'), (3,'Recrée des 3 curés','5168789654123654','08/27','458');");

$db->pdo->exec("INSERT INTO administrator_user (user_id) VALUES (1);");

$db->pdo->exec("INSERT INTO member_user (user_id, lastname, firstname, phone, pseudo, allows_notifications) VALUES (2, 'Chesnel', 'Yann', '0123456789', 'VeilleArbre', TRUE);");

$db->pdo->exec("INSERT INTO professional_user (user_id, code, denomination, siren) VALUES (3, 5462, 'SergeMytho and Co', '60622644000034'), (4, 7421, 'Fred port', '65941542000012'),(5,8452,'Rance Evasion','26915441000024'), (8, 9587, 'Brehat', '79658412354789'), (9, 7896, 'Récrée des 3 curés', '12548965324785'), (10, 1489, 'La vallée des Saints', '25489600358897'), (6, 9635, 'VoyageurGuidé', '95489433452897');");

$db->pdo->exec("INSERT INTO public_professional (pro_id) VALUES (3), (8), (10), (6);");

$db->pdo->exec("INSERT INTO private_professional (pro_id, last_veto, payment_id) VALUES (4, '2024-11-30', 1),(5,'2024-11-30',2),(9, '2024-09-20', 3);");

// ---------------------------------------------------------------------- //
// create offer types
// ---------------------------------------------------------------------- //

$db->pdo->exec("INSERT INTO offer_type (id, type, price) VALUES (1, 'standard', 4.99), (2, 'premium', 7.99), (3, 'gratuite', 0.00);");


// ---------------------------------------------------------------------- //
// create tags
// ---------------------------------------------------------------------- //

$tags = [
    'restaurant' => ['Française', 'Fruit de mer', 'Plastique', 'Italienne', 'Indienne', 'Gastronomique', 'Restauration rapide', 'Crêperie'],
    'others' => ['Culturel', 'Gastronomie', 'Patrimoine', 'Musée', 'Histoire', 'Atelier', 'Urbain', 'Musique', 'Nature', 'Famille', 'Plein air', 'Cirque', 'Sport', 'Son et lumière', 'Nautique', 'Humour'],
];

$tagsIds = [];

foreach ($tags as $tagType => $tagValues) {

    foreach ($tagValues as $tagValue) {
        $tagModel = new OfferTag();
        $tagModel->name = strtolower($tagValue);
        $tagModel->save();
        $tagsIds[$tagType][] = $tagModel->id;
    }

}


// ---------------------------------------------------------------------- //
// create offer
// ---------------------------------------------------------------------- //

$offre1 = new Offer();
$offre1->title = "Café des Halles";
$offre1->summary = "Le Café des Halles est un lieu convivial situé au cœur d'une ville, souvent à proximité d'un marché couvert ou d'une place animée. Ce café est un point de rencontre privilégié pour les habitants et les visiteurs, offrant une atmosphère chaleureuse et accueillante.";
$offre1->description = 'le Café des Halles se distingue par son ambiance authentique et son cadre pittoresque, souvent proche d\'une halle ou d\'un marché. C\'est un endroit idéal pour faire une pause après une matinée de courses ou pour prendre un café en terrasse tout en observant l\'agitation de la ville. Avec son service amical, c\'est un lieu où l\'on se sent chez soi, que ce soit pour un petit-déjeuner rapide, un déjeuner décontracté, ou un apéritif en soirée.';
$offre1->likes = 57;
$offre1->offline = 0;
$offre1->offline_date = null;
$offre1->last_online_date = "2024-11-01";
$offre1->view_counter = 120;
$offre1->click_counter = 180;
$offre1->website = 'https://www.facebook.com/people/Caf%C3%A9-Des-Halles/100064099743039/';
$offre1->phone_number = '0296371642';
$offre1->category = 'restaurant';
$offre1->professional_id = 3;
$offre1->address_id = 21;
$offre1->offer_type_id = 1;
$offre1->save();

//type offres
$db->pdo->exec("INSERT INTO restaurant_offer (offer_id, url_image_carte, range_price) VALUES (" . $offre1->id . ", 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/21/f5/07/e3/cafe-des-halles.jpg?w=700&h=-1&s=1',3);");

//repas
$repas1 = new Meal();
$repas1->name = 'Galette complète';
$repas1->save();

$repas2 = new Meal();
$repas2->name = 'Uncle IPA';
$repas2->save();

$repas3 = new Meal();
$repas3->name = 'Crèpe beurre sucre';
$repas3->save();

$repas4 = new Meal();
$repas4->name = 'Andouille de Guémené accompagnée d’une purée de carrotte';
$repas4->save();

$repas5 = new Meal();
$repas5->name = 'Breizh Tea';
$repas5->save();

$repas6 = new Meal();
$repas6->name = 'Far breton';
$repas6->save();

RestaurantOffer::findOne(['offer_id' => $offre1->id])->addMeal($repas1->meal_id);
RestaurantOffer::findOne(['offer_id' => $offre1->id])->addMeal($repas2->meal_id);
RestaurantOffer::findOne(['offer_id' => $offre1->id])->addMeal($repas3->meal_id);
RestaurantOffer::findOne(['offer_id' => $offre1->id])->addMeal($repas4->meal_id);
RestaurantOffer::findOne(['offer_id' => $offre1->id])->addMeal($repas5->meal_id);
RestaurantOffer::findOne(['offer_id' => $offre1->id])->addMeal($repas6->meal_id);

//add tags
for ($i = 0; $i < 4; $i++) {
    $tag = $tagsIds['restaurant'][array_rand($tagsIds['restaurant'])];
    if (!in_array($tag, $offre1->tags())) {
        $offre1->addTag($tag);
    }
}

$horaire1o1 = new OfferSchedule();
$horaire1o1->day = 1;
$horaire1o1->opening_hours = '12:00';
$horaire1o1->closing_hours = '23:00';
$horaire1o1->save();
$horaire2o1 = new OfferSchedule();
$horaire2o1->day = 2;
$horaire2o1->opening_hours = '12:00';
$horaire2o1->closing_hours = '23:00';
$horaire2o1->save();
$horaire3o1 = new OfferSchedule();
$horaire3o1->day = 3;
$horaire3o1->opening_hours = 'fermé';
$horaire3o1->closing_hours = 'fermé';
$horaire3o1->save();
$horaire4o1 = new OfferSchedule();
$horaire4o1->day = 4;
$horaire4o1->opening_hours = '12:00';
$horaire4o1->closing_hours = '23:00';
$horaire4o1->save();
$horaire5o1 = new OfferSchedule();
$horaire5o1->day = 5;
$horaire5o1->opening_hours = '12:00';
$horaire5o1->closing_hours = '23:00';
$horaire5o1->save();
$horaire6o1 = new OfferSchedule();
$horaire6o1->day = 6;
$horaire6o1->opening_hours = '19:30';
$horaire6o1->closing_hours = '23:00';
$horaire6o1->save();
$horaire7o1 = new OfferSchedule();
$horaire7o1->day = 7;
$horaire7o1->opening_hours = 'fermé';
$horaire7o1->closing_hours = 'fermé';
$horaire7o1->save();

RestaurantOffer::findOne(['offer_id' => $offre1->id])->addSchedule($horaire1o1->id);
RestaurantOffer::findOne(['offer_id' => $offre1->id])->addSchedule($horaire2o1->id);
RestaurantOffer::findOne(['offer_id' => $offre1->id])->addSchedule($horaire3o1->id);
RestaurantOffer::findOne(['offer_id' => $offre1->id])->addSchedule($horaire4o1->id);
RestaurantOffer::findOne(['offer_id' => $offre1->id])->addSchedule($horaire5o1->id);
RestaurantOffer::findOne(['offer_id' => $offre1->id])->addSchedule($horaire6o1->id);
RestaurantOffer::findOne(['offer_id' => $offre1->id])->addSchedule($horaire7o1->id);

//create village gaulois
$offre2 = new Offer();
$offre2->title = "Village Gaulois";
$offre2->summary = 'Voyagez dans le temps et plongez au cœur de la vie gauloise, où vous pourrez découvrir des artisans passionnés, participer à des jeux anciens et vivre une expérience immersive pour toute la famille dans un cadre authentique et amusant.';
$offre2->description = 'Découvrez le Village Gaulois au sein du parc du Radôme et profitez de plus de 20 jeux originaux, distractifs et éducatifs. Apprentissage et divertissement sont les maîtres mots au Village Gaulois.
Sur place, la restauration est possible à la crêperie du village : "le Menhir Gourmand" !
Ce village n’est pas celui du célèbre petit Gaulois... C’est un lieu de détente, de culture et de distraction. Mais les habitants du village restent irréductibles car, avec ténacité depuis 25 ans, ils mènent une action de solidarité en direction d’une lointaine région du monde, l’Afrique.';
$offre2->likes = 420;
$offre2->offline = 0;
$offre2->offline_date = null;
$offre2->last_online_date = "2024-11-01";
$offre2->view_counter = 8714;
$offre2->click_counter = 1234;
$offre2->website = 'https://www.levillagegaulois.org/php/home.php';
$offre2->phone_number = '0296918395';
$offre2->category = 'attraction_park';
$offre2->offer_type_id = 2;
$offre2->professional_id = 4;
$offre2->address_id = 22;
$offre2->save();

$db->pdo->exec("INSERT INTO attraction_park_offer (offer_id, url_image_park_map, attraction_number, required_age) VALUES (" . $offre2->id . ", 'https://www.village-gaulois.org/wp-content/uploads/2024/05/VILLAGE-GAULOIS-plan.webp', 20, 3);");

//add tags
for ($i = 0; $i < 2; $i++) {
    $tag = $tagsIds['others'][array_rand($tagsIds['others'])];
    if (!in_array($tag, $offre2->tags())) {
        $offre2->addTag($tag);
    }
}

$horaire1o2 = new OfferSchedule();
$horaire1o2->day = 1;
$horaire1o2->opening_hours = 'fermé';
$horaire1o2->closing_hours = 'fermé';
$horaire1o2->save();
$horaire2o2 = new OfferSchedule();
$horaire2o2->day = 2;
$horaire2o2->opening_hours = '09:00';
$horaire2o2->closing_hours = '19:00';
$horaire2o2->save();
$horaire3o2 = new OfferSchedule();
$horaire3o2->day = 3;
$horaire3o2->opening_hours = '09:00';
$horaire3o2->closing_hours = '19:00';
$horaire3o2->save();
$horaire4o2 = new OfferSchedule();
$horaire4o2->day = 4;
$horaire4o2->opening_hours = 'fermé';
$horaire4o2->closing_hours = 'fermé';
$horaire4o2->save();
$horaire5o2 = new OfferSchedule();
$horaire5o2->day = 5;
$horaire5o2->opening_hours = '09:00';
$horaire5o2->closing_hours = '19:00';
$horaire5o2->save();
$horaire6o2 = new OfferSchedule();
$horaire6o2->day = 6;
$horaire6o2->opening_hours = '09:00';
$horaire6o2->closing_hours = '20:00';
$horaire6o2->save();
$horaire7o2 = new OfferSchedule();
$horaire7o2->day = 7;
$horaire7o2->opening_hours = '09:00';
$horaire7o2->closing_hours = '20:00';
$horaire7o2->save();

AttractionParkOffer::findOne(['offer_id' => $offre2->id])->addSchedule($horaire1o2->id);
AttractionParkOffer::findOne(['offer_id' => $offre2->id])->addSchedule($horaire2o2->id);
AttractionParkOffer::findOne(['offer_id' => $offre2->id])->addSchedule($horaire3o2->id);
AttractionParkOffer::findOne(['offer_id' => $offre2->id])->addSchedule($horaire4o2->id);
AttractionParkOffer::findOne(['offer_id' => $offre2->id])->addSchedule($horaire5o2->id);
AttractionParkOffer::findOne(['offer_id' => $offre2->id])->addSchedule($horaire6o2->id);
AttractionParkOffer::findOne(['offer_id' => $offre2->id])->addSchedule($horaire7o2->id);


$offre3 = new Offer();
$offre3->title = "Promenade en Bateau sur le Canal de la Rance";
$offre3->summary = 'Découvrez la beauté paisible du canal de la Rance entre Dinan et Saint-Samson-sur-Rance à bord de notre confortable bateau.';
$offre3->description = 'Avec un petit comité de seulement 10 personnes, profitez d\'une expérience intime et relaxante en naviguant à travers des paysages pittoresques. Parfait pour une escapade tranquille, notre bateau vous offre une perspective unique sur cette région magnifique.
Nous nous rendrons jusqu\'au Port de Dinan (pas d\'arrêt, pas de débarquement). Lors de notre promenade vous pourrez observer l\'avifaune de la Plaine de Taden et en apprendre plus sur le patrimoine naturel de la Vallée de la Rance. Votre Capitaine vous racontera également des anecdotes historiques sur l\'évolution de la Rance au fil des années.';
$offre3->likes = 8;
$offre3->offline = 0;
$offre3->offline_date = null;
$offre3->last_online_date = "2024-11-01";
$offre3->view_counter = 321;
$offre3->click_counter = 180;
$offre3->website = 'https://www.rance-evasion.fr/';
$offre3->phone_number = '0786912239';
$offre3->offer_type_id = 1;
$offre3->professional_id = 5;
$offre3->address_id = 23;
$offre3->category = 'visit';
$offre3->save();

$db->pdo->exec("INSERT INTO visit_offer (offer_id, duration, guide) VALUES (" . $offre3->id . ", 1.5, true);");

$db->pdo->exec("INSERT INTO visit_language (offer_id, language) VALUES (" . $offre3->id . ", 'français'), (" . $offre3->id . ", 'anglais')");

//add tags
for ($i = 0; $i < 3; $i++) {
    $tag = $tagsIds['others'][array_rand($tagsIds['others'])];
    if (!in_array($tag, $offre3->tags())) {
        $offre3->addTag($tag);
    }
}

//create cachette de merlin
$offre4 = new Offer();
$offre4->title = "La cachette de Merlin";
$offre4->summary = 'Voyagez dans le temps et plongez au cœur de la vie gauloise, où vous pourrez découvrir des artisans passionnés, participer à des jeux anciens et vivre une expérience immersive pour toute la famille dans un cadre authentique et amusant.';
$offre4->description = 'La légende raconte que Merlin, banni du Royaume de Camelot, est venu s\'installer dans ce mystérieux manoir où il éleva avec amour deux enfants prénommés Arthur et Morgane. Aujourd\'hui, le vieux manoir recèle un esprit enchanteur et déborde de surprises !';
$offre4->likes = 600;
$offre4->offline = 0;
$offre4->offline_date = null;
$offre4->last_online_date = "2024-11-02";
$offre4->view_counter = 8714;
$offre4->click_counter = 8001;
$offre4->website = 'https://www.lacachettedemerlin.fr/';
$offre4->phone_number = '0698834022';
$offre4->category = 'show';
$offre4->offer_type_id = 2;
$offre4->professional_id = 6;
$offre4->address_id = 24;
$offre4->save();

$db->pdo->exec("INSERT INTO offer_period (id, start_date,end_date) VALUES (1,'2024-06-01', '2024-09-01');");

$db->pdo->exec("INSERT INTO show_offer (offer_id, duration, capacity, period_id) VALUES (" . $offre4->id . ", 1.5, 33, 1);");

//no tags




//create balade bréhat
$offre5 = new Offer();
$offre5->title = "Traversée et tour de l'île de Brehat";
$offre5->summary = 'Embarquez à bord de l’un de nos navires pour une traversée ou un tour de l’île. Naviguez loin du flot touristique au cœur d’une zone NATURA 2000.';
$offre5->description = 'Profitez de notre formule TOUR DE L’ÎLE pour une balade commentée et animée par des passionnés. Vous pourrez admirer l’île par la mer, ses rochers roses, sa côte sauvage et son patrimoine maritime exceptionnel. Les 96 ilots de l’archipel vous offriront un panorama incroyable.
A l’issue, vous débarquerez sur l’île de Bréhat pour une visite libre. Le retour s’effectuera avec les traversées directes.';
$offre5->likes = 4012;
$offre5->offline = 0;
$offre5->offline_date = null;
$offre5->last_online_date = "2024-10-01";
$offre5->view_counter = 20986;
$offre5->click_counter = 7863;
$offre5->website = 'https://surmerbrehat.com/';
$offre5->phone_number = '0677980042';
$offre5->category = 'activity';
$offre5->offer_type_id = 2;
$offre5->professional_id = 8;
$offre5->address_id = 16;
$offre5->save();

$db->pdo->exec("INSERT INTO activity_offer (offer_id, duration, required_age) VALUES ($offre5->id, 1.0, 3);");

//add tags
for ($i = 0; $i < 3; $i++) {
    $tag = $tagsIds['others'][array_rand($tagsIds['others'])];
    if (!in_array($tag, $offre5->tags())) {
        $offre5->addTag($tag);
    }
}

$horaire1o5 = new OfferSchedule();
$horaire1o5->day = 1;
$horaire1o5->opening_hours = '09:00';
$horaire1o5->closing_hours = '17:00';
$horaire1o5->save();
$horaire2o5 = new OfferSchedule();
$horaire2o5->day = 2;
$horaire2o5->opening_hours = '09:00';
$horaire2o5->closing_hours = '17:00';
$horaire2o5->save();
$horaire3o5 = new OfferSchedule();
$horaire3o5->day = 3;
$horaire3o5->opening_hours = 'fermé';
$horaire3o5->closing_hours = 'fermé';
$horaire3o5->save();
$horaire4o5 = new OfferSchedule();
$horaire4o5->day = 4;
$horaire4o5->opening_hours = 'fermé';
$horaire4o5->closing_hours = 'fermé';
$horaire4o5->save();
$horaire5o5 = new OfferSchedule();
$horaire5o5->day = 5;
$horaire5o5->opening_hours = '09:00';
$horaire5o5->closing_hours = '17:00';
$horaire5o5->save();
$horaire6o5 = new OfferSchedule();
$horaire6o5->day = 6;
$horaire6o5->opening_hours = '09:00';
$horaire6o5->closing_hours = '17:00';
$horaire6o5->save();
$horaire7o5 = new OfferSchedule();
$horaire7o5->day = 7;
$horaire7o5->opening_hours = '09:00';
$horaire7o5->closing_hours = '17:00';
$horaire7o5->save();

ActivityOffer::findOne(['offer_id' => $offre5->id])->addSchedule($horaire1o5->id);
ActivityOffer::findOne(['offer_id' => $offre5->id])->addSchedule($horaire2o5->id);
ActivityOffer::findOne(['offer_id' => $offre5->id])->addSchedule($horaire3o5->id);
ActivityOffer::findOne(['offer_id' => $offre5->id])->addSchedule($horaire4o5->id);
ActivityOffer::findOne(['offer_id' => $offre5->id])->addSchedule($horaire5o5->id);
ActivityOffer::findOne(['offer_id' => $offre5->id])->addSchedule($horaire6o5->id);
ActivityOffer::findOne(['offer_id' => $offre5->id])->addSchedule($horaire7o5->id);




//create Récrée Curés
$offre6 = new Offer();
$offre6->title = "La Récrée des 3 Curés";
$offre6->summary = 'Amateurs de sensations fortes, venez profiter au plus grand parc d’attractions breton la Récrée des trois curés';
$offre6->description = 'Plongez dans l’aventure au Parc d’attractions La Récré des Trois Curés à Milizac ! Sur ses 17 hectares, des dizaines d’attractions captivantes attendent petits et grands. Des manèges palpitants aux aires de jeux dédiées aux plus petits, ce lieu offre une évasion totale dans un cadre verdoyant. Adultes, ados et enfants, tous y trouveront leur bonheur pour une journée d’amusement garantie.';
$offre6->likes = 100841;
$offre6->offline = 0;
$offre6->offline_date = null;
$offre6->last_online_date = "2024-09-01";
$offre6->view_counter = 542321;
$offre6->click_counter = 35874;
$offre6->website = 'https://www.larecredes3cures.com/';
$offre6->phone_number = '0298079559';
$offre6->category = 'attraction_park';
$offre6->offer_type_id = 2;
$offre6->professional_id = 9;
$offre6->address_id = 17;
$offre6->save();

$db->pdo->exec("INSERT INTO attraction_park_offer (offer_id, url_image_park_map, attraction_number, required_age) VALUES (" . $offre6->id . ", 'https://www.parc-attraction.eu/wp-content/uploads/2023/02/la-recre-des-3-cures-plan.png', 38, 3);");


$horaire1o6 = new OfferSchedule();
$horaire1o6->day = 1;
$horaire1o6->opening_hours = '09:00';
$horaire1o6->closing_hours = '19:00';
$horaire1o6->save();
$horaire2o6 = new OfferSchedule();
$horaire2o6->day = 2;
$horaire2o6->opening_hours = '09:00';
$horaire2o6->closing_hours = '19:00';
$horaire2o6->save();
$horaire3o6 = new OfferSchedule();
$horaire3o6->day = 3;
$horaire3o6->opening_hours = 'fermé';
$horaire3o6->closing_hours = 'fermé';
$horaire3o6->save();
$horaire4o6 = new OfferSchedule();
$horaire4o6->day = 4;
$horaire4o6->opening_hours = 'fermé';
$horaire4o6->closing_hours = 'fermé';
$horaire4o6->save();
$horaire5o6 = new OfferSchedule();
$horaire5o6->day = 5;
$horaire5o6->opening_hours = '09:00';
$horaire5o6->closing_hours = '19:00';
$horaire5o6->save();
$horaire6o6 = new OfferSchedule();
$horaire6o6->day = 6;
$horaire6o6->opening_hours = '09:00';
$horaire6o6->closing_hours = '20:00';
$horaire6o6->save();
$horaire7o6 = new OfferSchedule();
$horaire7o6->day = 7;
$horaire7o6->opening_hours = '09:00';
$horaire7o6->closing_hours = '20:00';
$horaire7o6->save();

AttractionParkOffer::findOne(['offer_id' => $offre6->id])->addSchedule($horaire1o6->id);
AttractionParkOffer::findOne(['offer_id' => $offre6->id])->addSchedule($horaire2o6->id);
AttractionParkOffer::findOne(['offer_id' => $offre6->id])->addSchedule($horaire3o6->id);
AttractionParkOffer::findOne(['offer_id' => $offre6->id])->addSchedule($horaire4o6->id);
AttractionParkOffer::findOne(['offer_id' => $offre6->id])->addSchedule($horaire5o6->id);
AttractionParkOffer::findOne(['offer_id' => $offre6->id])->addSchedule($horaire6o6->id);
AttractionParkOffer::findOne(['offer_id' => $offre6->id])->addSchedule($horaire7o6->id);

//add tags
for ($i = 0; $i < 3; $i++) {
    $tag = $tagsIds['others'][array_rand($tagsIds['others'])];
    if (!in_array($tag, $offre6->tags())) {
        $offre6->addTag($tag);
    }
}




// ---------------------------------------------------------------------- //
// photos offre1
// ---------------------------------------------------------------------- //

$photosCafe1 = new OfferPhoto();
$photosCafe1->url_photo = 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/1c/e7/89/7e/cafe-des-halles.jpg?w=1000&h=-1&s=1';
$photosCafe1->offer_id = $offre1->id;
$photosCafe1->save();


$photosCafe2 = new OfferPhoto();
$photosCafe2->url_photo = 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/1c/62/5d/a4/cafe-des-halles.jpg?w=800&h=-1&s=1';
$photosCafe2->offer_id = $offre1->id;
$photosCafe2->save();

$photosCafe3 = new OfferPhoto();
$photosCafe3->url_photo = 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/25/16/c0/23/nos-plats.jpg?w=1000&h=-1&s=1';
$photosCafe3->offer_id = $offre1->id;
$photosCafe3->save();

$photosCafe4 = new OfferPhoto();
$photosCafe4->url_photo = 'https://media-cdn.tripadvisor.com/media/photo-s/1b/80/31/6a/cafe-des-halles.jpg';
$photosCafe4->offer_id = $offre1->id;
$photosCafe4->save();

$photosCafe5 = new OfferPhoto();
$photosCafe5->url_photo = 'https://img.lacarte.menu/storage/media/company_gallery/8769476/conversions/contribution_gallery.jpg';
$photosCafe5->offer_id = $offre1->id;
$photosCafe5->save();

$photosCafe5 = new OfferPhoto();
$photosCafe5->url_photo = 'https://menu.restaurantguru.com/m9/Cafe-Des-Halles-Lannion-menu.jpg';
$photosCafe5->offer_id = $offre1->id;
$photosCafe5->save();

// ---------------------------------------------------------------------- //
// photos offre2
// ---------------------------------------------------------------------- //

$photosGaulois1 = new OfferPhoto();
$photosGaulois1->url_photo = 'https://res.cloudinary.com/funbooker/image/upload/c_fill,dpr_2.0,f_auto,q_auto/v1/marketplace-listing/eiq9bgtjemwjwtweimn7';
$photosGaulois1->offer_id = $offre2->id;
$photosGaulois1->save();

$photosGaulois2 = new OfferPhoto();
$photosGaulois1->url_photo = 'https://media.ouest-france.fr/v1/pictures/MjAyMzA1ZDZjYzI3YmMyOGVkMWY3NTYzMzVmZmM3MWU1NjIwNWI?width=1260&height=708&focuspoint=50%2C25&cropresize=1&client_id=bpeditorial&sign=a23fba2fa83ab698eb62df2cdd611baefe85167b31937fff8a81de57d51e9d46';
$photosGaulois1->offer_id = $offre2->id;
$photosGaulois1->save();

$photosGaulois3 = new OfferPhoto();
$photosGaulois3->url_photo = 'https://static.actu.fr/uploads/2023/07/camp-romain-village-gaulois.jpg';
$photosGaulois3->offer_id = $offre2->id;
$photosGaulois3->save();

$photosGaulois4 = new OfferPhoto();
$photosGaulois4->url_photo = 'https://www.village-gaulois.org/wp-content/uploads/2024/06/unsiteuniqueeneurope-image-pecheurs.jpg';
$photosGaulois4->offer_id = $offre2->id;
$photosGaulois4->save();

$photosGaulois5 = new OfferPhoto();
$photosGaulois5->url_photo = 'https://desbretonsencavale.fr/wp-content/uploads/2020/04/Realisation_du_15-04-202-page-14-1024x759.jpg';
$photosGaulois5->offer_id = $offre2->id;
$photosGaulois5->save();

// ---------------------------------------------------------------------- //
// photos offre3
// ---------------------------------------------------------------------- //

$photosPromenade1 = new OfferPhoto();
$photosPromenade1->url_photo = 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/2c/ae/1c/32/a-bord-du-meissa.jpg?w=1200&h=-1&s=1';
$photosPromenade1->offer_id = $offre3->id;
$photosPromenade1->save();

$photosPromenade2 = new OfferPhoto();
$photosPromenade2->url_photo = 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/2c/ae/15/59/a-bord-du-meissa.jpg?w=1200&h=-1&s=1';
$photosPromenade2->offer_id = $offre3->id;
$photosPromenade2->save();

$photosPromenade3 = new OfferPhoto();
$photosPromenade3->url_photo = 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/2c/ae/13/9b/a-bord-du-meissa.jpg?w=1200&h=-1&s=1';
$photosPromenade3->offer_id = $offre3->id;
$photosPromenade3->save();

$photosPromenade4 = new OfferPhoto();
$photosPromenade4->url_photo = 'https://cdn.getyourguide.com/img/tour/109168e365f79c9d75ab72c48f149892b169638a99c7eaa800fabac810f57feb.jpg/145.jpg';
$photosPromenade4->offer_id = $offre3->id;
$photosPromenade4->save();

$photosPromenade5 = new OfferPhoto();
$photosPromenade5->url_photo = 'https://www.rance-evasion.fr/wp-content/uploads/2024/03/DJI_0712-scaled.jpg';
$photosPromenade5->offer_id = $offre3->id;
$photosPromenade5->save();

// ---------------------------------------------------------------------- //
// photos offre4
// ---------------------------------------------------------------------- //

$photosMerlin1 = new OfferPhoto();
$photosMerlin1->url_photo = 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/22/df/7d/9b/merlin-remet-le-diplome.jpg?w=1000&h=-1&s=1';
$photosMerlin1->offer_id = $offre4->id;
$photosMerlin1->save();

$photosMerlin2 = new OfferPhoto();
$photosMerlin2->url_photo = 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/19/58/90/fa/merlin-conte-la-legende.jpg?w=1400&h=-1&s=1';
$photosMerlin2->offer_id = $offre4->id;
$photosMerlin2->save();

$photosMerlin3 = new OfferPhoto();
$photosMerlin3->url_photo = 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/22/df/7c/ca/merlin-rencontra-viviane.jpg?w=1400&h=-1&s=1';
$photosMerlin3->offer_id = $offre4->id;
$photosMerlin3->save();

$photosMerlin4 = new OfferPhoto();
$photosMerlin4->url_photo = 'https://static.actu.fr/uploads/2019/08/25424-190822121728584-1-960x640.jpg';
$photosMerlin4->offer_id = $offre4->id;
$photosMerlin4->save();

$photosMerlin5 = new OfferPhoto();
$photosMerlin5->url_photo = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRujC_IRF2QiR9g3kjT9fMUnmjbBq7QfoV9qGuhe2Ou1JDXxlkXrM1uB1dHcx4x_fdaWRI&usqp=CAU';
$photosMerlin5->offer_id = $offre4->id;
$photosMerlin5->save();

// ---------------------------------------------------------------------- //
// photos offre5
// ---------------------------------------------------------------------- //

$photoBrehat1 = new OfferPhoto();
$photoBrehat1->url_photo = 'https://www.theisland-list.com/wp-content/uploads/2023/05/brehat1.jpg';
$photoBrehat1->offer_id = $offre5->id;
$photoBrehat1->save();

$photoBrehat2 = new OfferPhoto();
$photoBrehat2->url_photo = 'https://cdt22.media.tourinsoft.eu/upload/Brehat-2020-06-ile-du-guerzido-oeil-de-paco.jpg';
$photoBrehat2->offer_id = $offre5->id;
$photoBrehat2->save();

$photoBrehat3 = new OfferPhoto();
$photoBrehat3->url_photo = 'https://cdn.artphotolimited.com/images/58bd704f04799b000f623d31/1000x1000/ile-de-brehat-2.jpg';
$photoBrehat3->offer_id = $offre5->id;
$photoBrehat3->save();

$photoBrehat4 = new OfferPhoto();
$photoBrehat4->url_photo = 'https://cdt29.media.tourinsoft.eu/upload/Brehat-drone-002.jpg';
$photoBrehat4->offer_id = $offre5->id;
$photoBrehat4->save();

$photoBrehat5 = new OfferPhoto();
$photoBrehat5->url_photo = 'https://img-4.linternaute.com/WYRJv5KYR2MkNuOVphX2H4vknYg=/1500x/smart/9e14e631d7e341a1b9dee022c5b9d91f/ccmcms-linternaute/27556522.jpg';
$photoBrehat5->offer_id = $offre5->id;
$photoBrehat5->save();

// ---------------------------------------------------------------------- //
// photos offre6
// ---------------------------------------------------------------------- //

$photoRecree1 = new OfferPhoto();
$photoRecree1->url_photo = 'https://www.parcs-france.com/wp-content/uploads/parc-recredes3cures-ouverture-tarif-nouveaute.jpg';
$photoRecree1->offer_id = $offre6->id;
$photoRecree1->save();

$photoRecree2 = new OfferPhoto();
$photoRecree2->url_photo = 'https://29.recreatiloups.com/wp-content/uploads/sites/3/2014/10/spoontus-recre-milizac.jpg';
$photoRecree2->offer_id = $offre6->id;
$photoRecree2->save();

$photoRecree3 = new OfferPhoto();
$photoRecree3->url_photo = 'https://www.brest-terres-oceanes.fr/wp-content/uploads/2018/06/DSC03057.jpg';
$photoRecree3->offer_id = $offre6->id;
$photoRecree3->save();

$photoRecree4 = new OfferPhoto();
$photoRecree4->url_photo = 'https://media.letelegramme.fr/api/v1/images/view/63b9e6efbbe84b391253d21b/web_golden_xl/63b9e6efbbe84b391253d21b.1';
$photoRecree4->offer_id = $offre6->id;
$photoRecree4->save();

$photoRecree5 = new OfferPhoto();
$photoRecree5->url_photo = 'https://www.larecredes3cures.com/app/uploads/2024/03/aquatico-scaled-1600x784-c-center.jpg';
$photoRecree5->offer_id = $offre6->id;
$photoRecree5->save();



// ---------------------------------------------------------------------- //
// photos offre7
// ---------------------------------------------------------------------- //
$photovalleedessaints1 = new OfferPhoto();
$photovalleedessaints1->url_photo = 'https://www.francetvinfo.fr/pictures/Q1e3C7l3TscaP5lbRMjbNRTynVk/fit-in/720x/2019/08/11/phpHz9llI.jpg';
$photovalleedessaints1->offer_id = $offre7->id;
$photovalleedessaints1->save();
$photovalleedessaints2 = new OfferPhoto();
$photovalleedessaints2->url_photo = 'https://www.tourismebretagne.com/app/uploads/crt-bretagne/2018/10/la-vallee-des-saints-3-640x480.jpg';
$photovalleedessaints2->offer_id = $offre7->id;
$photovalleedessaints2->save();
$photovalleedessaints3 = new OfferPhoto();
$photovalleedessaints3->url_photo = 'https://static.actu.fr/uploads/2020/09/img-1876-960x640.jpg';
$photovalleedessaints3->offer_id = $offre7->id;
$photovalleedessaints3->save();
$photovalleedessaints4 = new OfferPhoto();
$photovalleedessaints4->url_photo = 'https://www.tourismebretagne.com/app/uploads/crt-bretagne/2024/04/thumbs/Vallee%20des%20Saints_YB-640x480-crop-1715068176.jpg';
$photovalleedessaints4->offer_id = $offre7->id;
$photovalleedessaints4->save();
$photovalleedessaints5 = new OfferPhoto();
$photovalleedessaints5->url_photo = 'https://www.lavalleedessaints.bzh/img/presentations/vallee-from-sky.webp';
$photovalleedessaints5->offer_id = $offre7->id;
$photovalleedessaints5->save();

echo "Database seeded successfully.\n";

?>