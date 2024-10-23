<?php

use app\core\Application;
use app\models\account\Account;
use app\models\offer\Offer;
use app\models\offer\OfferPhoto;
use app\models\offer\OfferTag;

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

$password = password_hash("1234", PASSWORD_DEFAULT);



$db->pdo->exec("TRUNCATE TABLE address, offer_tag, offer_photo, offer_option, offer, offer_type, private_professional, public_professional, professional_user, member_user, administrator_user, user_account, anonymous_account, account, mean_of_payment RESTART IDENTITY CASCADE;");

// ---------------------------------------------------------------------- //
// user adress
// ---------------------------------------------------------------------- //

$db->pdo->exec("INSERT INTO address (id, number, street, city, postal_code, longitude, latitude) VALUES 
                                                                    (11,68, 'Avenue Millies Lacroix', 'Eaubonne', 95600,2.2779189 ,48.992128), 
                                                                    (12,90,'rue de Lille','Asnières-sur-Seine',92600,2.285369,48.914155),
                                                                    (13,44,'rue de la Hulotais','Saint-priest',69800,4.947071,45.698938),
                                                                    (14,47,'boulevard Bryas',' Dammarie-les-lys',77190,	2.634831,48.515451),
                                                                    (15, 1, 'Rue de la Mairie', 'Lannion', 22300, -3.4597, 48.7326),
                                                                    (16, 1,'Crec’h Kerrio', 'Île-de-Bréhat', 22870, 48.84070603138791, -2.999732772564104),
                                                                    (17, 1,'La Récré des 3 Curés', 'Les Trois Cures', 29290, 48.47492014209391, -4.526581655177133),
                                                                    (18, 1,'La Vallée des Saints', 'Carnoët', 22160, 48.84070603138791, -2.999732772564104)
                                                                    ;");

// ---------------------------------------------------------------------- //
// create offer adress
// ---------------------------------------------------------------------- //

$db->pdo->exec("INSERT INTO address (id, number, street, city, postal_code, longitude, latitude) VALUES 
                                                                    (21, 2, 'Rue des Halles', 'Lannion', 22300, -3.4597,48.7326 ), 
                                                                    (22, 1, 'Parc du Radôme', 'Pleumeur-Bodou', 22560, 	-3.474171, 	48.800755),
                                                                    (23, 1, 'Parking du plan deau', 'Samson-sur-Rance', 22100, -3.4597, 48.7326),
                                                                    (24, 0, 'Crec’h Kerrio', 'Île-de-Bréhat', 22870, 48.84070603138791, -2.999732772564104);");


// ---------------------------------------------------------------------- //
// create users
// ---------------------------------------------------------------------- //
$db->pdo->exec("INSERT INTO account (id) VALUES (1), (2), (3), (4), (5), (6), (7), (8), (9), (10);");
$db->pdo->exec("INSERT INTO user_account (account_id, mail, password, avatarUrl, address_id) VALUES 
                                                                     (1, 'rouevictor@gmail.com', '" . $password . "','https://i.pinimg.com/control/564x/a2/a9/fd/a2a9fdfb77c19cc7b5e1749718228945.jpg',11), 
                                                                     (2, 'eliaz.chesnel@outlook.fr', '" . $password . "', 'https://preview.redd.it/4l7yhfrppsh51.jpg?width=640&crop=smart&auto=webp&s=11445a8cd85d7b4e81170491d3f013e5599048ae',12), 
                                                                     (3, 'sergelemytho@gmail.com','" . $password . "','https://media.gqmagazine.fr/photos/5e135c806b02b40008e0d316/1:1/w_1600%2Cc_limit/thumbnail_sergemytho.jpg',13),
                                                                     (5, 'rance.evasion@gmail.com', '" . $password . "', 'https://fr.web.img5.acsta.net/pictures/16/05/17/12/17/360795.jpg', 15),
                                                                     (4, 'fredlechat@gmail.com', '" . $password . "', 'https://i.chzbgr.com/full/10408722944/hDAD92EF6/ole',14),
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

$db->pdo->exec("INSERT INTO professional_user (user_id, code, denomination, siren) VALUES (3, 5462, 'SergeMytho and Co', '60622644000034'), (4, 7421, 'Fred port', '65941542000012'),(5,8452,'Rance Evasion','26915441000024'), (8, 9587, 'Brehat', '79658412354789'), (9, 7896, 'Récrée des 3 curés', '12548965324785'), (10, 1489, 'La vallée des Saints', '25489600358897');");

$db->pdo->exec("INSERT INTO public_professional (pro_id) VALUES (3), (8), (10);");

$db->pdo->exec("INSERT INTO private_professional (pro_id, last_veto, payment_id) VALUES (4, '2024-11-30', 1),(5,'2024-11-30',2),(9, '2024-09-20', 3);");

// ---------------------------------------------------------------------- //
// create offer types
// ---------------------------------------------------------------------- //

$db->pdo->exec("INSERT INTO offer_type (id, type, price) VALUES (1, 'standard', 4.99), (2, 'premium', 7.99), (3, 'gratuite', 0.00);");

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
$db->pdo->exec("INSERT INTO restaurant_offer (offer_id, url_image_carte, minimum_price, maximum_price) VALUES (" . $offre1->id . ", 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/21/f5/07/e3/cafe-des-halles.jpg?w=700&h=-1&s=1', 12, 20);");

//add tags
$tagsCafe = ['café', 'bar', 'brasserie', 'terrasse', 'convivial', 'burger', 'chaleureux', 'vege friendly', 'familial'];

foreach ($tagsCafe as $tagName) {
    $tagName = strtolower($tagName);
    $tag = OfferTag::findOne(['name' => $tagName]);
    if (!$tag) {
        $tag = new OfferTag();
        $tag->name = $tagName;
        $tag->save();
    }
}

foreach ($tagsCafe as $tagName) {
    $tagName = strtolower($tagName);
    $tagModel = OfferTag::findOne(['name' => $tagName]);
    if ($tagModel) {
        $offre1->addTag($tagModel->id);
    } else {
        echo "Tag '$tagName' not found.\n";
    }
}
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre1->id . ", 1, '12h', '23h')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre1->id . ", 2, '12h', '23h')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre1->id . ", 3, 'fermé', 'fermé')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre1->id . ", 4, '12h', '23h')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre1->id . ", 5, '12h', '23h')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre1->id . ", 6, '19h30', '23h')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre1->id . ", 7, 'fermé', 'fermé')");

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
$tagsGaulois = ['jeux', 'artisanat', 'famille', 'éducatif', 'culture', 'crêperie', 'solidarité', 'détente'];

foreach ($tagsGaulois as $tagName) {
    $tagName = strtolower($tagName);
    $tag = OfferTag::findOne(['name' => $tagName]);
    if (!$tag) {
        $tag = new OfferTag();
        $tag->name = $tagName;
        $tag->save();
    }
}

foreach ($tagsGaulois as $tagName) {
    $tagName = strtolower($tagName);
    $tagModel = OfferTag::findOne(['name' => $tagName]);
    if ($tagModel) {
        $offre2->addTag($tagModel->id);
    } else {
        echo "Tag '$tagName' not found.\n";
    }
}

$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre2->id . ", 1, 'fermé', 'fermé')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre2->id . ", 2, '9h', '19h')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre2->id . ", 3, '9h', '19h')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre2->id . ", 4, 'fermé', 'fermé')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre2->id . ", 5, '9h', '19h')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre2->id . ", 6, '9h', '20h')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre2->id . ", 7, '9h', '20h')");

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

$tagsPromenade = ['bateau', 'canal', 'Dinan', 'Saint-Samson-sur-Rance', 'paysage', 'intime', 'relaxant', 'patrimoine'];


foreach ($tagsPromenade as $tagName) {
    $tagName = strtolower($tagName);
    $tag = OfferTag::findOne(['name' => $tagName]);
    if (!$tag) {
        $tag = new OfferTag();
        $tag->name = $tagName;
        $tag->save();
    }
}

foreach ($tagsPromenade as $tagName) {
    $tagName = strtolower($tagName);
    $tagModel = OfferTag::findOne(['name' => $tagName]);
    if ($tagModel) {
        $offre3->addTag($tagModel->id);
    } else {
        echo "Tag '$tagName' not found.\n";
    }
}

//create balade bréhat
$offre8 = new Offer();
$offre8->title = "Traversée et tour de l'île de Brehat";
$offre8->summary = 'Embarquez à bord de l’un de nos navires pour une traversée ou un tour de l’île. Naviguez loin du flot touristique au cœur d’une zone NATURA 2000.';
$offre8->description = 'Profitez de notre formule TOUR DE L’ÎLE pour une balade commentée et animée par des passionnés. Vous pourrez admirer l’île par la mer, ses rochers roses, sa côte sauvage et son patrimoine maritime exceptionnel. Les 96 ilots de l’archipel vous offriront un panorama incroyable.
A l’issue, vous débarquerez sur l’île de Bréhat pour une visite libre. Le retour s’effectuera avec les traversées directes.';
$offre8->likes = 4012;
$offre8->offline = 0;
$offre8->offline_date = null;
$offre8->last_online_date = "2024-10-01";
$offre8->view_counter = 20986;
$offre8->click_counter = 7863;
$offre8->website = 'https://surmerbrehat.com/';
$offre8->phone_number = '0677980042';
$offre8->category = 'activity';
$offre8->offer_type_id = 2;
$offre8->professional_id = 8;
$offre8->address_id = 16;
$offre8->save();

$db->pdo->exec("INSERT INTO activity_offer (offer_id, duration, required_age, price) VALUES ($offre8->id, 1.0, 3, 15.0);");

//add tags
$tagsBrehat = ['île', 'traversée', 'nature', 'patrimoine', 'maritime', 'guidée', 'panorama', 'granite rose'];

foreach ($tagsBrehat as $tagName) {
    $tagName = strtolower($tagName);
    $tag = OfferTag::findOne(['name' => $tagName]);
    if (!$tag) {
        $tag = new OfferTag();
        $tag->name = $tagName;
        $tag->save();
    }
}

foreach ($tagsBrehat as $tagName) {
    $tagName = strtolower($tagName);
    $tagModel = OfferTag::findOne(['name' => $tagName]);
    if ($tagModel) {
        $offre8->addTag($tagModel->id);
    } else {
        echo "Tag '$tagName' not found.\n";
    }
}



$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre8->id . ", 1, '9h', '17h')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre8->id . ", 2, '9h', '17h')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre8->id . ", 3, 'fermé', 'fermé')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre8->id . ", 4, 'fermé', 'fermé')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre8->id . ", 5, '9h', '17h')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre8->id . ", 6, '9h', '17h')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre8->id . ", 7, '9h', '17h')");


$offre9 = new Offer();
$offre9->title = "La Récrée des 3 Curés";
$offre9->summary = 'Amateurs de sensations fortes, venez profiter au plus grand parc d’attractions breton la Récrée des trois curés';
$offre9->description = 'Plongez dans l’aventure au Parc d’attractions La Récré des Trois Curés à Milizac ! Sur ses 17 hectares, des dizaines d’attractions captivantes attendent petits et grands. Des manèges palpitants aux aires de jeux dédiées aux plus petits, ce lieu offre une évasion totale dans un cadre verdoyant. Adultes, ados et enfants, tous y trouveront leur bonheur pour une journée d’amusement garantie.';
$offre9->likes = 100841;
$offre9->offline = 0;
$offre9->offline_date = null;
$offre9->last_online_date = "2024-09-01";
$offre9->view_counter = 542321;
$offre9->click_counter = 35874;
$offre9->website = 'https://www.larecredes3cures.com/';
$offre9->phone_number = '0298079559';
$offre9->category = 'attraction_park';
$offre9->offer_type_id = 2;
$offre9->professional_id = 9;
$offre9->address_id = 17;
$offre9->save();

$db->pdo->exec("INSERT INTO attraction_park_offer (offer_id, url_image_park_map, attraction_number, required_age) VALUES (" . $offre9->id . ", 'https://www.parc-attraction.eu/wp-content/uploads/2023/02/la-recre-des-3-cures-plan.png', 38, 3);");

//add tags

$tagsRecree = ['attractions', 'sensations', 'manèges', 'enfants', 'famille', 'parc', 'verdoyant', 'évasion'];

$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre9->id . ", 1, '9h', '19h')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre9->id . ", 2, '9h', '19h')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre9->id . ", 3, 'fermé', 'fermé')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre9->id . ", 4, 'fermé', 'fermé')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre9->id . ", 5, '9h', '19h')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre9->id . ", 6, '9h', '20h')");
$db->pdo->exec("INSERT INTO offer_schedule (offer_id, day, opening_hours, closing_hours) VALUES (" . $offre9->id . ", 7, '9h', '20h')");

foreach ($tagsRecree as $tagName) {
    $tagName = strtolower($tagName);
    $tag = OfferTag::findOne(['name' => $tagName]);
    if (!$tag) {
        $tag = new OfferTag();
        $tag->name = $tagName;
        $tag->save();
    }
}

foreach ($tagsRecree as $tagName) {
    $tagName = strtolower($tagName);
    $tagModel = OfferTag::findOne(['name' => $tagName]);
    if ($tagModel) {
        $offre9->addTag($tagModel->id);
    } else {
        echo "Tag '$tagName' not found.\n";
    }
}


$offre10 = new Offer();
$offre10->title = "Vallée des Saints";
$offre10->summary = 'Il y a de la magie dans ce site hors norme. Un peu comme sur l’île de Pâques... mais à la manière bretonne ! ';
$offre10->description = 'Au coeur de Carnoët, vous vous promènerez au milieu de géants de granit. Le site compte 180 statues, dont certaines atteignent 7 mètres de haut ! Véritable jeu de piste pour les enfants, demandez-leur de trouver Sainte Riwanon ou Sainte Katell, et laissez place à votre imagination.';
$offre10->likes = 2014;
$offre10->offline = 0;
$offre10->offline_date = null;
$offre10->last_online_date = "2023-01-12";
$offre10->view_counter = 14856;
$offre10->click_counter = 3101;
$offre10->website = 'https://www.lavalleedessaints.com/';
$offre10->phone_number = '0296916226';
$offre10->category = 'visit_offer';
$offre10->offer_type_id = 1;
$offre10->professional_id = 10;
$offre10->address_id = 18;
$offre10->save();

$db->pdo->exec("INSERT INTO visit_offer (offer_id, duration, guide) VALUES (" . $offre10->id . ", 2.5, false);");

$db->pdo->exec("INSERT INTO visit_language (offer_id, language) VALUES (" . $offre10->id . ", 'français')");

//add tags

$tagsVallee = ['granit', 'statues', 'jeu de piste', 'enfants', 'imagination', 'culture bretonne'];


foreach ($tagsVallee as $tagName) {
    $tagName = strtolower($tagName);
    $tag = OfferTag::findOne(['name' => $tagName]);
    if (!$tag) {
        $tag = new OfferTag();
        $tag->name = $tagName;
        $tag->save();
    }
}

foreach ($tagsVallee as $tagName) {
    $tagName = strtolower($tagName);
    $tagModel = OfferTag::findOne(['name' => $tagName]);
    if ($tagModel) {
        $offre10->addTag($tagModel->id);
    } else {
        echo "Tag '$tagName' not found.\n";
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
$photosGaulois3->url_photo = 'https://www.levillagegaulois.org/php/img/accueil/accueil.jpg';
$photosGaulois3->offer_id = $offre2->id;
$photosGaulois3->save();

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

// ---------------------------------------------------------------------- //
// photos offre4
// ---------------------------------------------------------------------- //


// ---------------------------------------------------------------------- //
// photos offre8
// ---------------------------------------------------------------------- //

$photoBrehat1 = new OfferPhoto();
$photoBrehat1->url_photo = 'https://www.theisland-list.com/wp-content/uploads/2023/05/brehat1.jpg';
$photoBrehat1->offer_id = $offre8->id;
$photoBrehat1->save();

$photoBrehat2 = new OfferPhoto();
$photoBrehat2->url_photo = 'https://cdt22.media.tourinsoft.eu/upload/Brehat-2020-06-ile-du-guerzido-oeil-de-paco.jpg';
$photoBrehat2->offer_id = $offre8->id;
$photoBrehat2->save();

$photoBrehat3 = new OfferPhoto();
$photoBrehat3->url_photo = 'https://cdn.artphotolimited.com/images/58bd704f04799b000f623d31/1000x1000/ile-de-brehat-2.jpg';
$photoBrehat3->offer_id = $offre8->id;
$photoBrehat3->save();

// ---------------------------------------------------------------------- //
// photos offre9
// ---------------------------------------------------------------------- //

$photoRecree1 = new OfferPhoto();
$photoRecree1->url_photo = 'https://www.parcs-france.com/wp-content/uploads/parc-recredes3cures-ouverture-tarif-nouveaute.jpg';
$photoRecree1->offer_id = $offre9->id;
$photoRecree1->save();

$photoRecree2 = new OfferPhoto();
$photoRecree2->url_photo = 'https://29.recreatiloups.com/wp-content/uploads/sites/3/2014/10/spoontus-recre-milizac.jpg';
$photoRecree2->offer_id = $offre9->id;
$photoRecree2->save();

$photoRecree3 = new OfferPhoto();
$photoRecree3->url_photo = 'https://www.brest-terres-oceanes.fr/wp-content/uploads/2018/06/DSC03057.jpg';
$photoRecree3->offer_id = $offre9->id;
$photoRecree3->save();


// ---------------------------------------------------------------------- //
// photos offre9
// ---------------------------------------------------------------------- //

$photovalleedessaints1 = new OfferPhoto();
$photovalleedessaints1->url_photo = 'https://www.parcs-france.com/wp-content/uploads/parc-recredes3cures-ouverture-tarif-nouveaute.jpg';
$photovalleedessaints1->offer_id = $offre9->id;
$photovalleedessaints1->save();

$photovalleedessaints2 = new OfferPhoto();
$photovalleedessaints2->url_photo = 'https://29.recreatiloups.com/wp-content/uploads/sites/3/2014/10/spoontus-recre-milizac.jpg';
$photovalleedessaints2->offer_id = $offre9->id;
$photovalleedessaints2->save();

$photovalleedessaints3 = new OfferPhoto();
$photovalleedessaints3->url_photo = 'https://www.brest-terres-oceanes.fr/wp-content/uploads/2018/06/DSC03057.jpg';
$photovalleedessaints3->offer_id = $offre9->id;
$photovalleedessaints3->save();

echo "Database seeded successfully.\n";

?>