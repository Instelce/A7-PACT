<?php

use app\core\Application;
use app\models\account\Account;
use app\models\offer\Offer;

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

$db->pdo->exec("TRUNCATE TABLE offer_tag, offer_photo, offer_option, offer, offer_type, private_professional, public_professional, professional_user, member_user, administrator_user, user_account, anonymous_account, account CASCADE;");

$db->pdo->exec("INSERT INTO account (id) VALUES (1), (2), (3), (4);");
$db->pdo->exec("INSERT INTO user_account (account_id, mail, password, avatarUrl) VALUES (1, 'rouevictor@gmail.com', 'crazyyann','https://i.pinimg.com/control/564x/a2/a9/fd/a2a9fdfb77c19cc7b5e1749718228945.jpg'), (2, 'eliaz.chesnel@outlook.fr', 'plouzane29', 'https://preview.redd.it/4l7yhfrppsh51.jpg?width=640&crop=smart&auto=webp&s=11445a8cd85d7b4e81170491d3f013e5599048ae',(3, 'sergelemytho@gmail.com','louis16','https://media.gqmagazine.fr/photos/5e135c806b02b40008e0d316/1:1/w_1600%2Cc_limit/thumbnail_sergemytho.jpg'), (4, 'FredLeChat@gmail.com', 'croquettes', 'https://i.chzbgr.com/full/10408722944/hDAD92EF6/ole');");
$db->pdo->exec("INSERT INTO administrator_user (user_id) VALUES (1);");
$db->pdo->exec("INSERT INTO member_user (user_id, lastname, firstname, phone, pseudo, allows_notifications) VALUES (2, 'Chesnel', 'Yann', '0123456789', 'VeilleArbre', TRUE);");
$db->pdo->exec("INSERT INTO professional_user (user_id, code, denomination, siren) VALUES (3, 5462, 'SergeMytho and Co', '60622644000034'), (4, 7421, 'Fred port', '65941542000012');");
$db->pdo->exec("INSERT INTO public_professional (pro_id) VALUES (3);");
$db->pdo->exec("INSERT INTO private_professional (pro_id, last_veto) VALUES (4, '2024-11-31');");

//create offres
$db->pdo->exec("INSERT INTO offer_type (id, type, price) VALUES (1, 'standard', 4.99), (2, 'premium', 7.99);");

//create cafe des halles
$offre = new Offer();
$offre->id = 1;
$offre->title = "Café des Halles";
$offre->summary = "Le Café des Halles est un lieu convivial situé au cœur d'une ville, souvent à proximité d'un marché couvert ou d'une place animée. Ce café est un point de rencontre privilégié pour les habitants et les visiteurs, offrant une atmosphère chaleureuse et accueillante.";
$offre->description = 'le Café des Halles se distingue par son ambiance authentique et son cadre pittoresque, souvent proche d\'une halle ou d\'un marché. C\'est un endroit idéal pour faire une pause après une matinée de courses ou pour prendre un café en terrasse tout en observant l\'agitation de la ville. Avec son service amical, c\'est un lieu où l\'on se sent chez soi, que ce soit pour un petit-déjeuner rapide, un déjeuner décontracté, ou un apéritif en soirée.';
$offre->likes = 57;
$offre->offline = 0;
$offre->offline_date = '';
$offre->last_online_date = '2024-11-01';
$offre->view_counter = 120;
$offre->click_counter = 180;
$offre->website = 'https://www.facebook.com/people/Caf%C3%A9-Des-Halles/100064099743039/';
$offre->phone_number = '02 96 37 16 42';
$offre->offer_type_id = 1;
$offre->professional_id = 3;
$offre->save();