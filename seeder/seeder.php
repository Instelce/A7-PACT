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

// Truncate all tables
$db->pdo->exec("TRUNCATE TABLE offer_tag, offer_photo, offer_option, offer, offer_type, private_professional, public_professional, professional_user, member_user, administrator_user, user_account, anonymous_account, account CASCADE;");

// Create four users (admin, member, public-pro, private-pro)
$password = password_hash("1234", PASSWORD_DEFAULT);
$db->pdo->exec("INSERT INTO account (id) VALUES (1), (2), (3), (4);");
$db->pdo->exec("INSERT INTO user_account (account_id, mail, password, avatarUrl) VALUES (1, 'admin@example.com', '". $password ."', 'https://placehold.co/400'), (2, 'member@example.com', '". $password ."', 'https://placehold.co/400'), (3, 'public-pro@example.com', '". $password ."', 'https://placehold.co/400'), (4, 'private-pro@example.com', '". $password ."', 'https://placehold.co/400');");

$db->pdo->exec("INSERT INTO administrator_user (user_id) VALUES (1);");
$db->pdo->exec("INSERT INTO member_user (user_id, lastname, firstname, phone, pseudo, allows_notifications) VALUES (2, 'Doe', 'John', '0123456789', 'johndoe', TRUE);");
$db->pdo->exec("INSERT INTO professional_user (user_id, code, denomination, siren) VALUES (3, 1234, 'Toto corporate', '12345678901234'), (4, 5678, 'Super entreprise', '56789012345678');");
$db->pdo->exec("INSERT INTO public_professional (pro_id) VALUES (3);");
$db->pdo->exec("INSERT INTO private_professional (pro_id, last_veto) VALUES (4, '2021-01-01');");

// Create "standard" and "premium" offer types
$db->pdo->exec("INSERT INTO offer_type (id, type, price) VALUES (1, 'standard', 4.98), (2, 'premium', 7.98);");

// Create random offers for testing
for ($i = 0; $i < 10; $i++) {
    $offer = new Offer();
    $offer->title = "Offer $i";
    $offer->summary = "Summary $i";
    $offer->description = "Description $i";
    $offer->likes = rand(0, 100);
    $offer->offline = rand(0, 1);
    $offer->offline_date = "2021-01-0" . rand(1, 9);
    $offer->last_online_date = "2021-01-" . rand(10, 31);
    $offer->view_counter = rand(0, 100);
    $offer->click_counter = rand(0, 100);
    $offer->website = "https://example.com/$i";
    $offer->phone_number = "0123456789";
    $offer->offer_type_id = rand(1, 2);
    $offer->professional_id = rand(3, 4);
    $offer->save();
}

// Retrieve the offers ids
$offers_ids = $db->pdo->query("SELECT id FROM offer;")->fetchAll();

function getOfferId($i)
{
    global $offers_ids;
    return $offers_ids[$i - 1]['id'];
}

// Create specific offers (visit, restaurant, show, attraction_park, activity)
$db->pdo->exec("INSERT INTO visit_offer (offer_id, duration,  guide) VALUES (". getOfferId(1) . ", 2, false);");
$db->pdo->exec("INSERT INTO restaurant_offer (offer_id, url_image_carte, minimum_price, maximum_price) VALUES (". getOfferId(2) . ", 'https://placehold.co/400', 10, 30);");
$db->pdo->exec("INSERT INTO show_offer (offer_id, duration, capacity) VALUES (". getOfferId(3) . ", 3, 30);");
$db->pdo->exec("INSERT INTO attraction_park_offer (offer_id, url_image_park_map, attraction_number, required_age) VALUES (". getOfferId(4) . ", 'https://placehold.co/400', 10, 10);");
$db->pdo->exec("INSERT INTO activity_offer (offer_id, duration, required_age, price) VALUES (". getOfferId(5) . ", 2, 10, 10.99);");

echo "Database seeded successfully.\n";
