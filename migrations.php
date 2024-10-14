<?php

use app\core\Application;

require_once __DIR__ . '/vendor/autoload.php';

// setup env variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// setup config
$config = [
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD']
    ]
];

// setup app
$app = new Application(__DIR__, $config);

$app->db->applyMigration();