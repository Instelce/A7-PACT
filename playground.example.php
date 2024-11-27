<?php

// -------------------------------------------------------------------------------------------------
// Playground example file
//
// Copy this file to the root and rename it to `playground.php`
// and run it with `php playground.php`
// -------------------------------------------------------------------------------------------------

use app\core\Application;

require_once __DIR__ . '/vendor/autoload.php';

// Setup env variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Setup app
$app = new Application(dirname(__DIR__), [
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD']
    ]
]);

// You can do whatever you want here
// For example, you can create play with the database with models ...
