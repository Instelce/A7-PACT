<?php

use app\controllers\AuthController;
use app\core\Application;
use app\controllers\SiteController;

require_once __DIR__.'/../vendor/autoload.php';

// Setup env variables
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Setup hot reload
if ($_ENV['APP_ENVIRONMENT'] === 'dev') {
    new HotReloader\HotReloader('//localhost:8080/phrwatcher.php');
}

// Setup config
$config = [
    'style' => __DIR__.'/css/main.css',
    'userClass' => \app\models\User::class,
    'db' =>  [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD']
    ]
];

// Setup app
$app = new Application(dirname(__DIR__), $config);

$app->router->get('/', [SiteController::class, 'home']);

// Auth routes
$app->router->get('/login', [AuthController::class, 'login']);
$app->router->post('/login', [AuthController::class, 'login']);
$app->router->get('/register', [AuthController::class, 'register']);
$app->router->post('/register', [AuthController::class, 'register']);
$app->router->get('/logout', [AuthController::class, 'logout']);
$app->router->get('/profile', [AuthController::class, 'profile']);

$app->run();
