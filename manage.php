<?php

use app\core\Application;

require_once __DIR__ . '/vendor/autoload.php';

// setup env variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// parse command line arguments
if ($argc >= 2) {
    if ($argv[1] === "migration") {

        $config = [
            'userClass' => \app\models\User::class,
            'db' => [
                'dsn' => $_ENV['DB_DSN'],
                'user' => $_ENV['DB_USER'],
                'password' => $_ENV['DB_PASSWORD']
            ]
        ];

        // Setup app
        $app = new Application(__DIR__, $config);

        if ($argv[2] === "apply") {
            $app->db->applyMigration();
        } else if ($argv[2] === "drop") {
            $app->db->dropMigrations();
        } else {
            echo "Invalid argument" . PHP_EOL;
        }
    } else if ($argv[1] === "dev") {
        echo "Starting development tools" . PHP_EOL;
        exec("sh scripts/dev");
    } else if ($argv[1] === "dev-db") {
        if ($argv[2] === "start") {
            exec("sudo docker compose up -d");
            echo "Development database started" . PHP_EOL;
        } else if ($argv[2] === "stop") {
            exec("docker compose down > /dev/null 2>&1 &");
            echo "Development database stopped" . PHP_EOL;
        } else {
            echo "Invalid argument" . PHP_EOL;
        }
    } else if ($argv[1] === "tw") {
        echo "Starting tailwind watcher" . PHP_EOL;
        exec("sh scripts/tailwind");
    } else if ($argv[1] === "sf") {
        echo shell_exec("sh scripts/sync");
    } else {
        echo "Invalid argument" . PHP_EOL;
    }
} else {
    echo "Manage script" . PHP_EOL;
    echo PHP_EOL;
    echo "Usage:" . PHP_EOL;
    echo "  migration [apply|drop]" . PHP_EOL;
    echo "  dev: Start development server" . PHP_EOL;
    echo "  dev-db [start|stop]: Start/Stop development database" . PHP_EOL;
    echo "  tw: Start tailwind watcher" . PHP_EOL;
    echo "  sf: Synchronize your fork" . PHP_EOL;
}
