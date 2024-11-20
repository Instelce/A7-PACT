<?php

use app\controllers\AuthController;
use app\controllers\ApiController;
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
$app->router->get('/offres/<pk:int>', [\app\controllers\OfferController::class, 'detail']);
$app->router->post('/offres/<pk:int>', [\app\controllers\OfferController::class, 'detail']);
$app->router->get('/offres/<pk:int>/modification', [\app\controllers\OfferController::class, 'update']);
$app->router->post('/offres/<pk:int>/modification', [\app\controllers\OfferController::class, 'update']);
$app->router->get('/offres/<pk:int>/payment', [\app\controllers\OfferController::class, 'payment']);

// dashboard pro
$app->router->get('/dashboard', function () {
    if (Application::$app->user->isProfessional()) {
        Application::$app->response->redirect('/dashboard/offres');
    } else {
        Application::$app->response->redirect('/');
    }
});
$app->router->get('/dashboard/offres', [\app\controllers\DashboardController::class, 'offers']);
$app->router->get('/dashboard/avis', [\app\controllers\DashboardController::class, 'avis']);
$app->router->get('/dashboard/factures', [\app\controllers\DashboardController::class, 'factures']);

// Auth routes
$app->router->get('/connexion', [AuthController::class, 'login']);
$app->router->post('/connexion', [AuthController::class, 'login']);
$app->router->get('/inscription', [AuthController::class, 'register']);
$app->router->post('/inscription', [AuthController::class, 'register']);
$app->router->get('/inscription/professionnel/public', [AuthController::class, 'registerProfessionalPublic']);
$app->router->get('/inscription/professionnel/public', [AuthController::class, 'registerProfessionalPublic']);
$app->router->get('/deconnexion', [AuthController::class, 'logout']);
$app->router->get('/profile', [AuthController::class, 'profile']);

// Api routes
$app->router->get('/api/auth/user', [ApiController::class, 'user']);
$app->router->get('/api/offers', [ApiController::class, 'offers']);
$app->router->get('/api/opinions/<offer_id:int>', [ApiController::class, 'opinions']);


$app->run();