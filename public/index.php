<?php

use app\controllers\AuthController;
use app\core\Application;
use app\controllers\SiteController;
use app\models\account\UserAccount;


require_once __DIR__ . '/../vendor/autoload.php';

// Setup env variables
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Setup hot reload
//if ($_ENV['APP_ENVIRONMENT'] === 'dev') {
//    new HotReloader\HotReloader('//localhost:8080/phrwatcher.php');
//}

// Setup config
$config = [
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD']
    ]
];

// Setup app
$app = new Application(dirname(__DIR__), $config);

$app->router->get('/', [SiteController::class, 'home']);
$app->router->post('/', [SiteController::class, 'home']);
$app->router->get('/storybook', [SiteController::class, 'storybook']);
$app->router->get('/recherche', [SiteController::class, 'research']);

// Offer routes
$app->router->get('/offres/creation', [\app\controllers\OfferController::class, 'create']);
$app->router->post('/offres/creation', [\app\controllers\OfferController::class, 'create']);

// Auth routes
$app->router->get('/connexion', [AuthController::class, 'login']);
$app->router->post('/connexion', [AuthController::class, 'login']);
$app->router->get('/inscription', [AuthController::class, 'register']);
$app->router->post('/inscription', [AuthController::class, 'register']);
$app->router->get('/deconnexion', [AuthController::class, 'logout']);
$app->router->get('/profile', [AuthController::class, 'profile']);

$app->run();

