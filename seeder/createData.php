<?php

use app\core\Application;
use app\models\account\Account;
use app\models\offer\Offer;
use app\models\offer\OfferPhoto;

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
//create mean of payment
$db->pdo->exec("INSERT INTO mean_of_payment (id) VALUES (1);");
$db->pdo->exec("INSERT INTO cb_mean_of_payment (payment_id, name, card_number, expiration_date, cvv) VALUES (1, 'CB1', '1548759863254125', '07/25', '123');");

// ---------------------------------------------------------------------- //
// user adress
// ---------------------------------------------------------------------- //

$db->pdo->exec("INSERT INTO address (id, number, street, city, postal_code, longitude, latitude) VALUES 
                                                                    (11,68, 'Avenue Millies Lacroix', 'Eaubonne', 95600,2.2779189 ,48.992128), 
                                                                    (12,90,'rue de Lille','Asnières-sur-Seine',92600,2.285369,48.914155),
                                                                    (13,44,'rue de la Hulotais','Saint-priest',69800,4.947071,45.698938),
                                                                    (14,47,'boulevard Bryas',' Dammarie-les-lys',77190,	2.634831,48.515451)ON CONFLICT (id) DO NOTHING;");

// ---------------------------------------------------------------------- //
// create offer adress
// ---------------------------------------------------------------------- //

$db->pdo->exec("INSERT INTO address (id, number, street, city, postal_code, longitude, latitude) VALUES 
                                                                    (21, 2, 'Rue des Halles', 'Lannion', 22300, -3.4597,48.7326 ), 
                                                                    (22, 1, 'Rue de la Corderie', 'Lannion', 22300, -3.4597, 48.7326);");

// ---------------------------------------------------------------------- //
// create users
// ---------------------------------------------------------------------- //
$db->pdo->exec("INSERT INTO account (id) VALUES (1), (2), (3), (4);");
$db->pdo->exec("INSERT INTO user_account (account_id, mail, password, avatarUrl, address_id) VALUES 
                                                                     (1, 'rouevictor@gmail.com', '" . $password . "','https://i.pinimg.com/control/564x/a2/a9/fd/a2a9fdfb77c19cc7b5e1749718228945.jpg',11), 
                                                                     (2, 'eliaz.chesnel@outlook.fr', '" . $password . "', 'https://preview.redd.it/4l7yhfrppsh51.jpg?width=640&crop=smart&auto=webp&s=11445a8cd85d7b4e81170491d3f013e5599048ae',12), 
                                                                     (3, 'sergelemytho@gmail.com','" . $password . "','https://media.gqmagazine.fr/photos/5e135c806b02b40008e0d316/1:1/w_1600%2Cc_limit/thumbnail_sergemytho.jpg',13),
                                                                     (4, 'FredLeChat@gmail.com', '" . $password . "', 'https://i.chzbgr.com/full/10408722944/hDAD92EF6/ole',14);");

// ---------------------------------------------------------------------- //
// create means of payment
// ---------------------------------------------------------------------- //

$db->pdo->exec("INSERT INTO mean_of_payment (id) VALUES (1);");
$db->pdo->exec("INSERT INTO cb_mean_of_payment (payment_id, name, card_number, expiration_date, cvv) VALUES (1, 'CarteBancaire', '1548759863254125', '07/25', '123');");

$db->pdo->exec("INSERT INTO administrator_user (user_id) VALUES (1);");

$db->pdo->exec("INSERT INTO member_user (user_id, lastname, firstname, phone, pseudo, allows_notifications) VALUES (2, 'Chesnel', 'Yann', '0123456789', 'VeilleArbre', TRUE);");

$db->pdo->exec("INSERT INTO professional_user (user_id, code, denomination, siren) VALUES (3, 5462, 'SergeMytho and Co', '60622644000034'), (4, 7421, 'Fred port', '65941542000012');");

$db->pdo->exec("INSERT INTO public_professional (pro_id) VALUES (3);");

$db->pdo->exec("INSERT INTO private_professional (pro_id, last_veto, payment_id) VALUES (4, '2024-11-30', 1);");

// ---------------------------------------------------------------------- //
// create offer types
// ---------------------------------------------------------------------- //

$db->pdo->exec( "INSERT INTO offer_type (id, type, price) VALUES (1, 'standard', 4.99), (2, 'premium', 7.99);");

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
$offre1->offer_type_id = 1;
$offre1->professional_id = 3;
$offre1->address_id = 21;
$offre1->category = 'restaurant';
$offre1->save();

$db->pdo->exec("INSERT INTO restaurant_offer (offer_id, url_image_carte, minimum_price, maximum_price) VALUES (" . $offre1->id . ", 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/21/f5/07/e3/cafe-des-halles.jpg?w=700&h=-1&s=1', 12, 20);");


//create village gaulois
$offre2 = new Offer();
$offre2->title = "Village Gaulois";
$offre2->summary = 'Voyagez dans le temps et plongez au cœur de la vie gauloise, où vous pourrez découvrir des artisans passionnés, participer à des jeux anciens et vivre une expérience immersive pour toute la famille dans un cadre authentique et amusant.';
$offre2->description = 'Découvrez le Village Gaulois au sein du parc du Radôme et profitez de plus de 20 jeux originaux, distractifs et éducatifs. Apprentissage et divertissement sont les maîtres mots au Village Gaulois.
Sur place, la restauration est possible à la crêperie du village : "le Menhir Gourmand" !
Ce village n’est pas celui du célèbre petit Gaulois... C’est un lieu de détente, de culture et de distraction. Mais les habitants du village restent irréductibles car, avec ténacité depuis 25 ans, ils mènent une action de solidarité en direction d’une lointaine région du monde, l’Afrique.';
$offre2->likes = 2534;
$offre2->offline = 0;
$offre2->offline_date = '';
$offre2->last_online_date = "2024-11-01";
$offre2->view_counter = 8714;
$offre2->click_counter = 1234;
$offre2->website = 'https://www.levillagegaulois.org/php/home.php';
$offre2->phone_number = '02 96 91 83 95';
$offre2->offer_type_id = 2;
$offre2->professional_id = 4;
$offre2->address_id = 22;
$offre2->category = 'attraction_park';
$offre2->save();



//$db->pdo->exec("INSERT INTO offer(title, summary, description, likes, offline, offline_date, last_online_date, view_counter, click_counter, website, phone_number, offer_type_id, professional_id, address_id) VALUES ('Traversée de l île de Brehat', );");
$db->pdo->exec("INSERT INTO attraction_park_offer (offer_id, url_image_park_map, attraction_number, required_age) VALUES (" . $offre2->id . ", 'https://www.village-gaulois.org/wp-content/uploads/2024/05/VILLAGE-GAULOIS-plan.webp', 20, 3);");



// ---------------------------------------------------------------------- //
// photos offer
// ---------------------------------------------------------------------- //
$photosCafe1 = new OfferPhoto();
$photosCafe1->url_photo = 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/1c/e7/89/7e/cafe-des-halles.jpg?w=1000&h=-1&s=1';
$photosCafe1->offer_id = 1;
$photosCafe1->save();


$photosCafe2 = new OfferPhoto();
$photosCafe2->url_photo = 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/1c/62/5d/a4/cafe-des-halles.jpg?w=800&h=-1&s=1';
$photosCafe2->offer_id = 1;
$photosCafe2->save();

$photosCafe3 = new OfferPhoto();
$photosCafe3->url_photo = 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/25/16/c0/23/nos-plats.jpg?w=1000&h=-1&s=1';
$photosCafe3->offer_id = 1;
$photosCafe3->save();


$photosGaulois1 = new OfferPhoto();
$photosGaulois1->url_photo = 'https://res.cloudinary.com/funbooker/image/upload/c_fill,dpr_2.0,f_auto,q_auto/v1/marketplace-listing/eiq9bgtjemwjwtweimn7';
$photosGaulois1->offer_id = 2;
$photosGaulois1->save();

$photosGaulois2 = new OfferPhoto();
$photosGaulois1->url_photo = 'https://media.ouest-france.fr/v1/pictures/MjAyMzA1ZDZjYzI3YmMyOGVkMWY3NTYzMzVmZmM3MWU1NjIwNWI?width=1260&height=708&focuspoint=50%2C25&cropresize=1&client_id=bpeditorial&sign=a23fba2fa83ab698eb62df2cdd611baefe85167b31937fff8a81de57d51e9d46';
$photosGaulois1->offer_id = 2;
$photosGaulois1->save();

$photosGaulois3 = new OfferPhoto();
$photosGaulois3->url_photo = 'https://www.levillagegaulois.org/php/img/accueil/accueil.jpg';
$photosGaulois3->offer_id = 2;
$photosGaulois3->save();



echo "Database seeded successfully.\n";

?>