<?php

use app\core\Application;
use app\models\account\Account;
use app\models\offer\Offer;
use app\models\offer\OfferPhoto;
use app\models\offer\OfferStatusHistory;
use app\models\offer\OfferTag;
use app\models\offer\RestaurantOffer;
use app\models\offer\AttractionParkOffer;
use app\models\offer\ActivityOffer;
use app\models\Meal;
use app\models\offer\schedule\OfferSchedule;
use app\models\opinion\Opinion;
use app\models\payment\Invoice;
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

$password = password_hash("JuPeNoCs24", PASSWORD_DEFAULT);

function generateUsername($count = 10)
{
    $usernames = [];
    $firstNames = ['John', 'Jane', 'Michael', 'Emily', 'David', 'Sophia', 'Daniel', 'Emma', 'Chris', 'Olivia'];
    $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Martinez', 'Wilson'];
    $numbers = range(100, 999);

    for ($i = 0; $i < $count; $i++) {
        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];

        // You can adjust the format of usernames here
        $usernames[] = ["fullname" => "{$firstName}{$lastName}", "lastname" => $lastName, "firstname" => $firstName, "pseudo" => "{$firstName}{$lastName}" . $numbers[array_rand($numbers)]];
    }

    return $usernames;
}

function generatePhoneNumber()
{
    $phone = '0';
    for ($i = 0; $i < 9; $i++) {
        $phone .= rand(0, 9);
    }
    return $phone;
}

function randomOfferDate(): string
{
    return date('Y-m-d', rand(strtotime("2024-10-01"), strtotime("2024-11-30")));
}

$db->pdo->exec("TRUNCATE TABLE address, offer_tag, offer_photo, option, subscription, offer, offer_type, offer_period, private_professional, public_professional, professional_user, member_user, administrator_user, user_account, anonymous_account, account, mean_of_payment RESTART IDENTITY CASCADE;");

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
                                                                    (21, 2, 'Rue des Halles', 'Lannion', 22300, -3.4597, 48.7326), 
                                                                    (22, 1, 'Parc du Radôme', 'Pleumeur-Bodou', 22560, -3.5262799946878105, 48.784432468993565),
                                                                    (23, 1, 'Parking du plan deau', 'Samson-sur-Rance', 22100, -3.4597, 48.7326),
                                                                    (24, 13, 'Rue des Ruees', 'Tréhorenteuc', 56430, -2.2850415831640905, 48.00799182324886),
                                                                    (25, 7, 'Chau. des Corsaires', 'Saint-Malo', 35400, 48.64509219510429, -2.018348791710329),
                                                                    (26, 1, 'place abbé Gillard', 'Tréhorenteuc', 56430 , 48.007504883778765, -2.2872720618427955),
                                                                    (27, 34, 'Sentier des Douaniers', 'Plogoff', 29770 ,-4.6664956672893725, 48.03667645649522),
                                                                    (28, 1, 'All. de l\`Embarcadere', 'Baden', 56870, -2.8604925767306435, 47.60272463103174),
                                                                    (29, 3, 'Pl. Saint-Tanguy', 'Plougonvelin', 29217, 48.33125210141691, -4.7701230204747525),
                                                                    (16, 1, 'Crec’h Kerrio', 'Île-de-Bréhat', 22870, -2.999732772564104, 48.84070603138791),
                                                                    (17, 1, 'La Récré des 3 Curés', 'Les Trois Cures', 29290, -4.526581655177133, 48.47492014209391),
                                                                    (18, 1, 'La Vallée des Saints', 'Carnoët', 22160, -2.999732772564104, 48.84070603138791),
                                                                    (19, 3, 'Rue des potiers','Noyal-Châtillon-sur-Seiche', 35230, -1.6674224847189223, 48.041895277402126);");


// ---------------------------------------------------------------------- //
// create users
// ---------------------------------------------------------------------- //
for ($j = 0; $j <= 20; $j++) {
    $a = new Account();
    $a->save();
}
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
// create 10 members users
// ---------------------------------------------------------------------- //

$members = generateUsername(10);

foreach ($members as $i => $member) {
    $avatar = "https://avatar.iran.liara.run/public/" . rand(1, 100);
    //    $avatar = "https://ui-avatars.com/api/?size=128&name=" . $member['firstname'] . "+" . $member['lastname'];
    $db->pdo->exec("INSERT INTO user_account (account_id, mail, password, avatar_url, address_id) VALUES (" . 11 + $i . ", '" . $member['pseudo'] . "@gmail.com', '" . $password . "', '" . $avatar . "', 19);");
    $db->pdo->exec("INSERT INTO member_user (user_id, lastname, firstname, phone, pseudo, allows_notifications) VALUES (" . 11 + $i . ", '" . $member["firstname"] . "', '" . $member["lastname"] . "', '" . generatePhoneNumber() . "', '" . $member["pseudo"] . "', TRUE);");
}

// ---------------------------------------------------------------------- //
// create means of payment
// ---------------------------------------------------------------------- //

$db->pdo->exec("INSERT INTO mean_of_payment (id) VALUES (1), (2), (3);");
$db->pdo->exec("INSERT INTO cb_mean_of_payment (payment_id, name, card_number, expiration_date, cvv) VALUES (1, 'Fred port', '1548759863254125', '07/25', '123'),(2,'Rance Evasion','4287621589632154','08/29','123'), (3,'Recrée des 3 curés','5168789654123654','08/27','458');");

$db->pdo->exec("INSERT INTO administrator_user (user_id) VALUES (1);");

$db->pdo->exec("INSERT INTO member_user (user_id, lastname, firstname, phone, pseudo, allows_notifications) VALUES (2, 'Chesnel', 'Yann', '0123456789', 'VieilArbre', TRUE);");

$db->pdo->exec("INSERT INTO professional_user (user_id, code, denomination, siren, phone) VALUES (3, 5462, 'SergeMytho and Co', '60622644000034', '" . generatePhoneNumber() . "'), (4, 7421, 'Fred port', '65941542000012', '" . generatePhoneNumber() . "'),(5,8452,'Rance Evasion','26915441000024', '" . generatePhoneNumber() . "'), (8, 9587, 'Brehat', '79658412354789', '" . generatePhoneNumber() . "'), (9, 7896, 'Récrée des 3 curés', '12548965324785', '" . generatePhoneNumber() . "'), (10, 1489, 'La vallée des Saints', '25489600358897', '" . generatePhoneNumber() . "'), (6, 9635, 'VoyageurGuidé', '95489433452897', '" . generatePhoneNumber() . "');");

$db->pdo->exec("INSERT INTO public_professional (pro_id) VALUES (3), (8), (10), (6);");
//publics : sergemytho(3) ; brehat(8) ; -> valleedessaints(10) <-
//                                           plus utilisé

$db->pdo->exec("INSERT INTO private_professional (pro_id, last_veto, payment_id) VALUES (4, '2024-11-30', 1),(5,'2024-11-30',2),(9, '2024-09-20', 3);");
//privates : fredlechat(4) ; rance_evasion(5) ; recree_des_trois_cures(9) ; 

// ---------------------------------------------------------------------- //
// create offer types
// ---------------------------------------------------------------------- //

$db->pdo->exec("INSERT INTO offer_type (id, type, price) VALUES (1, 'standard', 1.67), (2, 'premium', 3.34), (3, 'gratuite', 0.00);");

// ---------------------------------------------------------------------- //
// create offer option
// ---------------------------------------------------------------------- //

$db->pdo->exec("INSERT INTO option (id, type, price) VALUES (1, 'en_relief', 8.34), (2, 'a_la_une', 16.68);");


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
$offre1->last_offline_date = null;
$offre1->offline_days = 0;
$offre1->view_counter = 120;
$offre1->click_counter = 180;
$offre1->website = 'https://www.facebook.com/people/Caf%C3%A9-Des-Halles/100064099743039/';
$offre1->phone_number = '0296371642';
$offre1->category = 'restaurant';
$offre1->professional_id = 3;
$offre1->address_id = 21;
$offre1->offer_type_id = 1;
$offre1->created_at = randomOfferDate();
$offre1->save();

//type offres
$db->pdo->exec("INSERT INTO restaurant_offer (offer_id, url_image_carte, range_price) VALUES (" . $offre1->id . ", 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/21/f5/07/e3/cafe-des-halles.jpg?w=700&h=-1&s=1',3);");

//repas
// $repas1 = new Meal();
// $repas1->name = 'Galette complète';
// $repas1->save();

// $repas2 = new Meal();
// $repas2->name = 'Uncle IPA';
// $repas2->save();

// $repas3 = new Meal();
// $repas3->name = 'Crèpe beurre sucre';
// $repas3->save();

// $repas4 = new Meal();
// $repas4->name = 'Andouille de Guémené accompagnée d’une purée de carrotte';
// $repas4->save();

// $repas5 = new Meal();
// $repas5->name = 'Breizh Tea';
// $repas5->save();

// $repas6 = new Meal();
// $repas6->name = 'Far breton';
// $repas6->save();

// RestaurantOffer::findOne(['offer_id' => $offre1->id])->addMeal($repas1->meal_id);
// RestaurantOffer::findOne(['offer_id' => $offre1->id])->addMeal($repas2->meal_id);
// RestaurantOffer::findOne(['offer_id' => $offre1->id])->addMeal($repas3->meal_id);
// RestaurantOffer::findOne(['offer_id' => $offre1->id])->addMeal($repas4->meal_id);
// RestaurantOffer::findOne(['offer_id' => $offre1->id])->addMeal($repas5->meal_id);
// RestaurantOffer::findOne(['offer_id' => $offre1->id])->addMeal($repas6->meal_id);

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
$offre2->last_offline_date = null;
$offre2->offline_days = 0;
$offre2->view_counter = 8714;
$offre2->click_counter = 1234;
$offre2->website = 'https://www.levillagegaulois.org/php/home.php';
$offre2->phone_number = '0296918395';
$offre2->category = 'attraction_park';
$offre2->offer_type_id = 2;
$offre2->professional_id = 4;
$offre2->address_id = 22;
$offre2->minimum_price = 15;
$offre2->created_at = randomOfferDate();
$offre2->save();
$offre2->addSubscription("a_la_une", date('Y-m-d', strtotime("last Monday")), 3);

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
$offre3->last_offline_date = null;
$offre3->offline_days = 0;
$offre3->view_counter = 321;
$offre3->click_counter = 180;
$offre3->website = 'https://www.rance-evasion.fr/';
$offre3->phone_number = '0786912239';
$offre3->offer_type_id = 1;
$offre3->professional_id = 5;
$offre3->address_id = 23;
$offre3->category = 'visit';
$offre3->minimum_price = 25;
$offre3->created_at = randomOfferDate();
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
$offre4->last_offline_date = null;
$offre4->offline_days = 0;
$offre4->view_counter = 8714;
$offre4->click_counter = 8001;
$offre4->website = 'https://www.lacachettedemerlin.fr/';
$offre4->phone_number = '0698834022';
$offre4->category = 'show';
$offre4->offer_type_id = 2;
$offre4->professional_id = 6;
$offre4->address_id = 24;
$offre4->minimum_price = 12;
$offre4->created_at = randomOfferDate();
$offre4->save();

$db->pdo->exec("INSERT INTO offer_period (id,offer_id, start_date,end_date) VALUES (1,$offre4->id,'2024-06-01', '2024-09-01');");

$db->pdo->exec("INSERT INTO show_offer (offer_id, duration, capacity) VALUES (" . $offre4->id . ", 1.5, 33);");

//no tags


//create balade bréhat
$offre5 = new Offer();
$offre5->title = "Traversée et tour de l'île de Brehat";
$offre5->summary = 'Embarquez à bord de l’un de nos navires pour une traversée ou un tour de l’île. Naviguez loin du flot touristique au cœur d’une zone NATURA 2000.';
$offre5->description = 'Profitez de notre formule TOUR DE L’ÎLE pour une balade commentée et animée par des passionnés. Vous pourrez admirer l’île par la mer, ses rochers roses, sa côte sauvage et son patrimoine maritime exceptionnel. Les 96 ilots de l’archipel vous offriront un panorama incroyable.
A l’issue, vous débarquerez sur l’île de Bréhat pour une visite libre. Le retour s’effectuera avec les traversées directes.';
$offre5->likes = 4012;
$offre5->offline = 0;
$offre5->last_offline_date = null;
$offre5->offline_days = 0;
$offre5->view_counter = 20986;
$offre5->click_counter = 7863;
$offre5->website = 'https://surmerbrehat.com/';
$offre5->phone_number = '0677980042';
$offre5->category = 'activity';
$offre5->offer_type_id = 2;
$offre5->professional_id = 8;
$offre5->address_id = 16;
$offre5->minimum_price = 35;
$offre5->created_at = randomOfferDate();
$offre5->save();
$offre5->addSubscription("a_la_une", date('Y-m-d', strtotime("last Monday")), 3);


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
$offre6->last_offline_date = null;
$offre6->offline_days = 0;
$offre6->view_counter = 542321;
$offre6->click_counter = 35874;
$offre6->website = 'https://www.larecredes3cures.com/';
$offre6->phone_number = '0298079559';
$offre6->category = 'attraction_park';
$offre6->offer_type_id = 2;
$offre6->professional_id = 9;
$offre6->address_id = 17;
$offre6->minimum_price = 18;
$offre6->created_at = randomOfferDate();
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

$offre7 = new Offer();
$offre7->title = "Visite guidée de la Cité Corsaire";
$offre7->summary = 'Plongez au cœur de l’histoire maritime de Saint-Malo avec un guide local passionné.';
$offre7->description = 'Explorez les remparts, les ruelles pavées et les trésors cachés de Saint-Malo. Votre guide vous racontera les histoires de corsaires, d’explorateurs et de la reconstruction de la ville après la Seconde Guerre mondiale.';
$offre7->likes = 215;
$offre7->offline = 0;
$offre7->last_offline_date = null;
$offre7->offline_days = 0;
$offre7->view_counter = 3260;
$offre7->click_counter = 1450;
$offre7->website = 'https://www.saint-malo-tourisme.com/';
$offre7->phone_number = '0299566699';
$offre7->offer_type_id = 1;
$offre7->professional_id = 4;
$offre7->address_id = 25;
$offre7->category = 'visit';
$offre7->minimum_price = 20;
$offre7->created_at = randomOfferDate();
$offre7->save();
$offre7->addSubscription("a_la_une", date('Y-m-d', strtotime("last Monday")), 3);


$db->pdo->exec("INSERT INTO visit_offer (offer_id, duration, guide) VALUES (" . $offre7->id . ", 1.5, true);");
$db->pdo->exec("INSERT INTO visit_language (offer_id, language) VALUES (" . $offre7->id . ", 'français'), (" . $offre3->id . ", 'anglais')");

//add tags
for ($i = 0; $i < 3; $i++) {
    $tag = $tagsIds['others'][array_rand($tagsIds['others'])];
    if (!in_array($tag, $offre6->tags())) {
        $offre6->addTag($tag);
    }
}

$offre8 = new Offer();
$offre8->title = "Balade contée en Forêt de Brocéliande";
$offre8->summary = 'Plongez dans l’univers des légendes arthuriennes lors d’une balade guidée en Forêt de Brocéliande.';
$offre8->description = 'Suivez un guide-conteur à travers les lieux emblématiques comme le Val sans Retour, la Fontaine de Jouvence, et le Tombeau de Merlin. Parfait pour les familles et les amateurs de mythes celtiques.';
$offre8->likes = 789;
$offre8->offline = 0;
$offre8->last_offline_date = null;
$offre8->offline_days = 0;
$offre8->view_counter = 4580;
$offre8->click_counter = 2300;
$offre8->website = 'https://www.broceliande-vacances.com/';
$offre8->phone_number = '0299798554';
$offre8->offer_type_id = 1;
$offre8->professional_id = 3;
$offre8->address_id = 26;
$offre8->category = 'visit';
$offre8->created_at = randomOfferDate();
$offre8->save();
$offre8->addSubscription("en_relief", date('Y-m-d', strtotime("last Monday")), 3);


$db->pdo->exec("INSERT INTO visit_offer (offer_id, duration, guide) VALUES (" . $offre8->id . ", 3.0, true);");
$db->pdo->exec("INSERT INTO visit_language (offer_id, language) VALUES (" . $offre8->id . ", 'français')");

//add tags
for ($i = 0; $i < 3; $i++) {
    $tag = $tagsIds['others'][array_rand($tagsIds['others'])];
    if (!in_array($tag, $offre6->tags())) {
        $offre6->addTag($tag);
    }
}
//privates : fredlechat(4) ; rance_evasion(5) ; recree_des_trois_cures(9)

$offre9 = new Offer();
$offre9->title = "Excursion à la Pointe du Raz";
$offre9->summary = 'Découvrez l’un des sites naturels les plus emblématiques de Bretagne, avec des falaises à couper le souffle et une vue imprenable sur l’Atlantique.';
$offre9->description = 'Explorez ce site classé Grand Site de France, connu pour ses paysages sauvages et ses sentiers côtiers. Une visite guidée vous permettra de mieux comprendre l’histoire et l’écosystème de ce lieu unique.';
$offre9->likes = 450;
$offre9->offline = 0;
$offre9->last_offline_date = null;
$offre9->offline_days = 0;
$offre9->view_counter = 3000;
$offre9->click_counter = 1200;
$offre9->website = 'https://www.pointe-du-raz.com/';
$offre9->phone_number = '0298920020';
$offre9->offer_type_id = 1;
$offre9->professional_id = 5;
$offre9->address_id = 27;
$offre9->category = 'visit';
$offre9->minimum_price = 12;
$offre9->created_at = randomOfferDate();
$offre9->save();

$db->pdo->exec("INSERT INTO visit_offer (offer_id, duration, guide) VALUES (" . $offre9->id . ", 2.5, true);");
$db->pdo->exec("INSERT INTO visit_language (offer_id, language) VALUES (" . $offre9->id . ", 'français'), (" . $offre9->id . ", 'anglais')");

//add tags
for ($i = 0; $i < 3; $i++) {
    $tag = $tagsIds['others'][array_rand($tagsIds['others'])];
    if (!in_array($tag, $offre6->tags())) {
        $offre6->addTag($tag);
    }
}

$offre10 = new Offer();
$offre10->title = "Croisière découverte du Golfe du Morbihan";
$offre10->summary = 'Partez à la découverte du Golfe du Morbihan, l’une des plus belles baies du monde, avec ses îles et ses paysages marins exceptionnels.';
$offre10->description = 'Embarquez pour une croisière commentée à travers les îles du Golfe du Morbihan. Vous aurez l’occasion d’admirer l’Île aux Moines, l’Île d’Arz, et bien d’autres joyaux de cette baie unique.';
$offre10->likes = 1020;
$offre10->offline = 0;
$offre10->last_offline_date = null;
$offre10->offline_days = 0;
$offre10->view_counter = 6500;
$offre10->click_counter = 2800;
$offre10->website = 'https://www.golfedumorbihan.fr/';
$offre10->phone_number = '0297636421';
$offre10->offer_type_id = 2;
$offre10->professional_id = 4;
$offre10->address_id = 28;
$offre10->category = 'activity';
$offre10->minimum_price = 25;
$offre10->created_at = randomOfferDate();
$offre10->save();
$offre10->addSubscription("a_la_une", date('Y-m-d', strtotime("last Monday")), 3);


$db->pdo->exec("INSERT INTO activity_offer (offer_id, duration, required_age) VALUES (" . $offre10->id . ", 3.0, 6);");

//add tags
for ($i = 0; $i < 3; $i++) {
    $tag = $tagsIds['others'][array_rand($tagsIds['others'])];
    if (!in_array($tag, $offre6->tags())) {
        $offre6->addTag($tag);
    }
}

$offre11 = new Offer();
$offre11->title = "La Crepe Dantel";
$offre11->summary = "La crêperie Dantel vous accueille sur le site exceptionnel de la Pointe Saint Mathieu et vous propose un large choix de crêpes blé noir et froment cuisinées avec des produits frais.";
$offre11->description = 'Située sur le site exceptionnel de la pointe Saint-Mathieu, un peu à l\'écart du phare, la Crêpe Dantel est désormais incontournable. C\'est en toute simplicité, qu\'à l\'heure du déjeuner du goûter ou du repas vous y dégusterez de délicieuses crêpes salées ou sucrées.';
$offre11->likes = 300;
$offre11->offline = 0;
$offre11->last_offline_date = null;
$offre11->offline_days = 0;
$offre11->view_counter = 120;
$offre11->click_counter = 180;
$offre11->website = 'http://fr-fr.facebook.com/lacrepedantel';
$offre11->phone_number = '0298402968';
$offre11->category = 'restaurant';
$offre11->professional_id = 4;
$offre11->address_id = 26;
$offre11->offer_type_id = 1;
$offre11->save();
$offre11->addSubscription("a_la_une", date('Y-m-d', strtotime("last Monday")), 3);

//type offres
$db->pdo->exec("INSERT INTO restaurant_offer (offer_id, url_image_carte, range_price) VALUES (" . $offre11->id . ", 'https://media-cdn.tripadvisor.com/media/photo-m/1280/1c/44/ac/3b/menu.jpg',3);");

//repas
// $repas1 = new Meal();
// $repas1->name = 'Galette complète';
// $repas1->save();

// $repas2 = new Meal();
// $repas2->name = 'Uncle IPA';
// $repas2->save();

// $repas3 = new Meal();
// $repas3->name = 'Crèpe beurre sucre';
// $repas3->save();

// $repas4 = new Meal();
// $repas4->name = 'Andouille de Guémené accompagnée d’une purée de carrotte';
// $repas4->save();

// $repas5 = new Meal();
// $repas5->name = 'Breizh Tea';
// $repas5->save();

// $repas6 = new Meal();
// $repas6->name = 'Far breton';
// $repas6->save();

// RestaurantOffer::findOne(['offer_id' => $offre11->id])->addMeal($repas1->meal_id);
// RestaurantOffer::findOne(['offer_id' => $offre11->id])->addMeal($repas2->meal_id);
// RestaurantOffer::findOne(['offer_id' => $offre11->id])->addMeal($repas3->meal_id);
// RestaurantOffer::findOne(['offer_id' => $offre11->id])->addMeal($repas4->meal_id);
// RestaurantOffer::findOne(['offer_id' => $offre11->id])->addMeal($repas5->meal_id);
// RestaurantOffer::findOne(['offer_id' => $offre11->id])->addMeal($repas6->meal_id);

//add tags
for ($i = 0; $i < 4; $i++) {
    $tag = $tagsIds['restaurant'][array_rand($tagsIds['restaurant'])];
    if (!in_array($tag, $offre11->tags())) {
        $offre11->addTag($tag);
    }
}

$horaire1o11 = new OfferSchedule();
$horaire1o11->day = 1;
$horaire1o11->opening_hours = '12:00';
$horaire1o11->closing_hours = '23:00';
$horaire1o11->save();
$horaire2o11 = new OfferSchedule();
$horaire2o11->day = 2;
$horaire2o11->opening_hours = '12:00';
$horaire2o11->closing_hours = '23:00';
$horaire2o11->save();
$horaire3o11 = new OfferSchedule();
$horaire3o11->day = 3;
$horaire3o11->opening_hours = 'fermé';
$horaire3o11->closing_hours = 'fermé';
$horaire3o11->save();
$horaire4o11 = new OfferSchedule();
$horaire4o11->day = 4;
$horaire4o11->opening_hours = '12:00';
$horaire4o11->closing_hours = '23:00';
$horaire4o11->save();
$horaire5o11 = new OfferSchedule();
$horaire5o11->day = 5;
$horaire5o11->opening_hours = '12:00';
$horaire5o11->closing_hours = '23:00';
$horaire5o11->save();
$horaire6o11 = new OfferSchedule();
$horaire6o11->day = 6;
$horaire6o11->opening_hours = '19:30';
$horaire6o11->closing_hours = '23:00';
$horaire6o11->save();
$horaire7o11 = new OfferSchedule();
$horaire7o11->day = 7;
$horaire7o11->opening_hours = 'fermé';
$horaire7o11->closing_hours = 'fermé';
$horaire7o11->save();

RestaurantOffer::findOne(['offer_id' => $offre11->id])->addSchedule($horaire1o11->id);
RestaurantOffer::findOne(['offer_id' => $offre11->id])->addSchedule($horaire2o11->id);
RestaurantOffer::findOne(['offer_id' => $offre11->id])->addSchedule($horaire3o11->id);
RestaurantOffer::findOne(['offer_id' => $offre11->id])->addSchedule($horaire4o11->id);
RestaurantOffer::findOne(['offer_id' => $offre11->id])->addSchedule($horaire5o11->id);
RestaurantOffer::findOne(['offer_id' => $offre11->id])->addSchedule($horaire6o11->id);
RestaurantOffer::findOne(['offer_id' => $offre11->id])->addSchedule($horaire7o11->id);

// ---------------------------------------------------------------------- //
// photos offre11
// ---------------------------------------------------------------------- //

$offre11->addPhoto('https://cdt29.media.tourinsoft.eu/upload/la-crepe-dantel.JPG');
$offre11->addPhoto('https://cdt29.media.tourinsoft.eu/upload/crepe-0cad6daa38a643599b9bcd44703c0d7d.JPG');
$offre11->addPhoto('https://dynamic-media-cdn.tripadvisor.com/media/photo-o/18/92/91/22/20190726-142709-largejpg.jpg?w=900&h=500&s=1');
$offre11->addPhoto('https://cdt29.media.tourinsoft.eu/upload/table-9.JPG');
$offre11->addPhoto('https://cdt29.media.tourinsoft.eu/upload/la-crepe-dantel.JPG');
$offre11->addPhoto('https://media-cdn.tripadvisor.com/media/photo-m/1280/1c/44/ac/3b/menu.jpg');


// ---------------------------------------------------------------------- //
// photos offre1
// ---------------------------------------------------------------------- //

$offre1->addPhoto('https://dynamic-media-cdn.tripadvisor.com/media/photo-o/1c/e7/89/7e/cafe-des-halles.jpg?w=1000&h=-1&s=1');
$offre1->addPhoto('https://dynamic-media-cdn.tripadvisor.com/media/photo-o/1c/62/5d/a4/cafe-des-halles.jpg?w=800&h=-1&s=1');
$offre1->addPhoto('https://dynamic-media-cdn.tripadvisor.com/media/photo-o/25/16/c0/23/nos-plats.jpg?w=1000&h=-1&s=1');
$offre1->addPhoto('https://media-cdn.tripadvisor.com/media/photo-s/1b/80/31/6a/cafe-des-halles.jpg');
$offre1->addPhoto('https://img.lacarte.menu/storage/media/company_gallery/8769476/conversions/contribution_gallery.jpg');
$offre1->addPhoto('https://menu.restaurantguru.com/m9/Cafe-Des-Halles-Lannion-menu.jpg');



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

$photoMalo = new OfferPhoto();
$photoMalo->url_photo = 'https://www.saint-malo-tourisme.com/app/uploads/saint-malo-tourisme/2022/06/thumbs/vue-sur-saint-malo-intra-muros-depuis-le-mole-des-noires---saint-malo-loic-lagarde-663-1200px-1920x960-crop-1654250785.jpg';
$photoMalo->offer_id = $offre7->id;
$photoMalo->save();

$photoMalo2 = new OfferPhoto();
$photoMalo2->url_photo = 'https://rennes.kidiklik.fr/sites/default/files/styles/crop_image/public/2024-06/visite%20guid%C3%A9e%20de%20la%20cit%C3%A9%20corsaire.jpg?itok=rQCkEYqI';
$photoMalo2->offer_id = $offre7->id;
$photoMalo2->save();

$photoMalo3 = new OfferPhoto();
$photoMalo3->url_photo = 'https://maville.com/photosmvi/2022/07/22/P31261938D5342611G.jpg';
$photoMalo3->offer_id = $offre7->id;
$photoMalo3->save();

// ---------------------------------------------------------------------- //
// photos offre8
// ---------------------------------------------------------------------- //

$photoBroce = new OfferPhoto();
$photoBroce->url_photo = 'https://static.broceliande.guide/IMG/jpg/balade_contee_a_l_arbre_d_or.jpg';
$photoBroce->offer_id = $offre8->id;
$photoBroce->save();

$photoBroce1 = new OfferPhoto();
$photoBroce1->url_photo = 'https://static.broceliande.guide/IMG/jpg/balade_contee_a_l_arbre_d_or.jpgttps://static.broceliande.guide/IMG/jpg/balade_contee_a_l_arbre_d_or.jpg';
$photoBroce1->offer_id = $offre8->id;
$photoBroce1->save();

$photoBroce2 = new OfferPhoto();
$photoBroce2->url_photo = 'https://www.broceliande-vacances.com/app/uploads/broceliande/2020/02/thumbs/broceliande-7019e-berthier2019-1920x960.jpg';
$photoBroce2->offer_id = $offre8->id;
$photoBroce2->save();

// ---------------------------------------------------------------------- //
// photos offre9
// ---------------------------------------------------------------------- //

$photoRaz = new OfferPhoto();
$photoRaz->url_photo = 'https://api.cloudly.space/resize/crop/1200/627/60/aHR0cHM6Ly9jZHQyOS5tZWRpYS50b3VyaW5zb2Z0LmV1L3VwbG9hZC9jcnRiLWFiNTQ0NEJELmpwZw==/image.jpg';
$photoRaz->offer_id = $offre9->id;
$photoRaz->save();

$photoRaz1 = new OfferPhoto();
$photoRaz1->url_photo = 'https://www.villagelaplage.com/wp-content/uploads/2021/02/decouverte-pointe-du-raz.jpg';
$photoRaz1->offer_id = $offre9->id;
$photoRaz1->save();

$photoRaz2 = new OfferPhoto();
$photoRaz2->url_photo = 'https://www.sentiersmaritimes.com/7440-thickbox_default/finistere-sauvage-de-la-presqu-ile-de-crozon-a-la-pointe-du-raz-.jpg';
$photoRaz2->offer_id = $offre9->id;
$photoRaz2->save();

$photoRaz3 = new OfferPhoto();
$photoRaz3->url_photo = 'https://media.ouest-france.fr/v1/pictures/MjAyMTAxZjUwOTQ4NzU0ZmVhMzBjOTljODY5NjAxN2IzOGE2N2Q?width=1260&height=708&focuspoint=50%2C25&cropresize=1&client_id=bpeditorial&sign=b6442f05df456089b237622dc21f039f9640549870cdabc808f3ffb98e145d41';
$photoRaz3->offer_id = $offre9->id;
$photoRaz3->save();

// ---------------------------------------------------------------------- //
// photos offre10
// ---------------------------------------------------------------------- //

$photoMorb = new OfferPhoto();
$photoMorb->url_photo = 'https://www.golfedumorbihan.bzh/content/uploads/2022/05/Rhuys2015_MG_1021.jpg';
$photoMorb->offer_id = $offre10->id;
$photoMorb->save();

$photoMorb2 = new OfferPhoto();
$photoMorb2->url_photo = 'https://www.domaine-de-kervallon.com/wp-content/uploads/2021/10/croisieres-izenag-bateau-morbihan-golf.jpg';
$photoMorb2->offer_id = $offre10->id;
$photoMorb2->save();

$photoMorb2 = new OfferPhoto();
$photoMorb2->url_photo = 'https://www.caseneuvemaxicatamaran.com/sites/anne-caseneuve.com/files/styles/photo_contenu/public/2023-02/50264907-2CC2-487F-A8E6-C00A5D321481.jpeg?itok=Kw-53kpg';
$photoMorb2->offer_id = $offre10->id;
$photoMorb2->save();

$photoMorb3 = new OfferPhoto();
$photoMorb3->url_photo = 'https://www.navix.fr/wp-content/uploads/2023/03/NAVIX_Golfe-ile-aux-moines_by-zulaan.net_-scaled.jpg';
$photoMorb3->offer_id = $offre10->id;
$photoMorb3->save();


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
        ],
        [
            "title" => "Trop cher pour ce que c'est",
            "content" => "Les portions étaient minuscules, et la qualité ne justifiait pas du tout les prix exorbitants. Je ne recommande pas.",
            "rating" => 1,
            "activity" => "restaurant"
        ],
        [
            "title" => "Évitez à tout prix",
            "content" => "La nourriture était immangeable, et il y avait une forte odeur de renfermé dans la salle. Horrible expérience.",
            "rating" => 1,
            "activity" => "restaurant"
        ],
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
        [
            "title" => "Ne vaut pas le détour",
            "content" => "Rien de fascinant ici. Les bâtiments sont en mauvais état, et il n’y a aucune ambiance. Très ennuyant.",
            "rating" => 2,
            "activity" => "visit"
        ],
        [
            "title" => "Grosse déception",
            "content" => "Le site n’a rien d’extraordinaire, et les panneaux explicatifs sont usés ou illisibles. Une vraie arnaque.",
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
        [
            "title" => "Manque de sécurité",
            "content" => "Certains manèges semblaient vieux et mal entretenus. Cela ne m’a pas rassuré, surtout pour les enfants.",
            "rating" => 1,
            "activity" => "attraction_park"
        ],
        [
            "title" => "Déception totale",
            "content" => "Le parc était sale, mal organisé et rien ne fonctionnait correctement. Nous n’y retournerons jamais.",
            "rating" => 1,
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
        [
            "title" => "Pas à la hauteur",
            "content" => "Les critiques étaient positives, mais ce spectacle était une vraie déception. Décors pauvres et musique assourdissante.",
            "rating" => 2,
            "activity" => "show"
        ],
        [
            "title" => "A éviter absolument",
            "content" => "Les sièges étaient inconfortables, et le spectacle n'avait aucun intérêt. Une soirée gâchée.",
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
        ],
        [
            "title" => "Une arnaque",
            "content" => "La description promettait une expérience inoubliable, mais c'était loin d'être le cas. Rien n’était à la hauteur.",
            "rating" => 1,
            "activity" => "activity"
        ],
        [
            "title" => "Un désastre",
            "content" => "Tout était mal organisé, et les conditions étaient déplorables. Nous avons quitté avant la fin.",
            "rating" => 1,
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
        $opinion->visit_date = date('Y-m-d', strtotime('-' . rand(1, 365) . ' days'));
        $opinion->created_at = date('Y-m-d H:i:s', strtotime('-' . rand(1, 365) . ' days'));
        $opinion->save();
        $account_ids[] = $opinion->account_id;

        $offer->rating = $offer->rating();
        $offer->update();
    }

}

// ---------------------------------------------------------------------------------------------- //
// Generate offer status histories
// ---------------------------------------------------------------------------------------------- //

foreach ($offers as $offer) {
    $date = $offer->created_at;
    $date = new DateTime($date);
    $date->modify('first day of this month');
    $today = new DateTime();
    $today->modify('first day of this month');

    while ($date < $today && $date->format("m") != $today->format("m")) {
        if (rand(0, 1) == 0) {
            $status = new OfferStatusHistory();
            $status->offer_id = $offer->id;
            $status->switch_to = $offer->offline ? "offline" : "online";
            $status->created_at = $date->format('Y-m-d');
            $status->save();

            $offer->offline = !$offer->offline;
            $offer->update();
        }

        $date->modify('+6 days');
    }
}


// ---------------------------------------------------------------------------------------------- //
// Generate invoice for offer of the each month since the creation of the offer
// ---------------------------------------------------------------------------------------------- //


foreach ($offers as $offer) {
    $date = $offer->created_at;
    $date = new DateTime($date);
    $date->modify('first day of this month');
    $today = new DateTime();
    $today->modify('first day of this month');

    while ($date < $today && $date->format("m") != $today->format("m")) {
        $invoice = new Invoice();
        $invoice->offer_id = $offer->id;
        $invoice->issue_date = $date->format('Y-m-d');
        $due_date = new DateTime($date->format('Y-m-d'));
        $invoice->due_date = $due_date->modify('+15 days')->format('Y-m-d');
        $invoice->service_date = $date->format("m");
        $invoice->save();

        $date->modify('+1 month');
    }
}


foreach ($offers as $offer) {
    if ($offer->offline) {
        $status = new OfferStatusHistory();
        $status->offer_id = $offer->id;
        $status->switch_to = "online";
        $status->created_at = date('Y-m-d');
        $status->save();

        $offer->offline = false;
        $offer->update();
    }
}


echo "Database seeded successfully.\n";

?>