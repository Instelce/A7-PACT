<?php
// -------------------------------------------------------------------------------------------------
// This file is used to manage the application from the command line
// -------------------------------------------------------------------------------------------------


use app\core\Application;
use app\core\TaskManager;

require_once __DIR__ . '/vendor/autoload.php';

// Setup env variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Setup app
try {
    $app = new Application(__DIR__, [
        'db' => [
            'dsn' => $_ENV['DB_DSN'],
            'user' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD']
        ]
    ]);
} catch (\PDOException $e) {}

// Parse command line arguments
if ($argc >= 2) {
    if ($argv[1] === "migration") {
        if ($argv[2] === "apply") {
            $app->db->applyMigration();
        } else if ($argv[2] === "drop") {
            $app->db->dropMigrations();
        } else if ($argv[2] === "fresh") {
            $app->db->dropMigrations();
            $app->db->applyMigration();
        } else {
            echo "Invalid argument" . PHP_EOL;
        }
    } else if ($argv[1] === "dev") {
        echo "Starting development tools" . PHP_EOL;
        exec("sh scripts/dev");
    } else if ($argv[1] === "dev-yann") {
        echo "Starting development tools" . PHP_EOL;
        exec("sh scripts/dev-yann");
    } else if ($argv[1] === "db") {
        if ($argv[2] === "start") {
            exec("sudo docker compose up -d");
            echo "Development database started" . PHP_EOL;
        } else if ($argv[2] === "stop") {
            exec("docker compose down > /dev/null 2>&1 &");
            echo "Development database stopped" . PHP_EOL;
        } else if ($argv[2] === "seed") {
            echo shell_exec("php seeder/seeder.php");
        } else {
            echo "Invalid argument" . PHP_EOL;
        }
    } else if ($argv[1] === "tw") {
        shell_exec("sh scripts/tw");
    } else if ($argv[1] === "sf") {
        echo shell_exec("sh scripts/sync");
    } else if ($argv[1] === "cron") {
        $taskManager = new TaskManager();
        $taskManager->scheduleTasks();
    } else if ($argv[1] === "task") {
        $taskManager = new TaskManager();
        if ($argv[2] === "list") {
            $taskManager->listTasks();
        } else {
            $taskManager->runTask($argv[2]);
        }
    } else {
        echo "Invalid argument" . PHP_EOL;
    }
} else {
    echo "Manage script" . PHP_EOL;
    echo PHP_EOL;
    echo "Usage:" . PHP_EOL;
    echo "  migration [apply|drop|fresh]" . PHP_EOL;
    echo "  dev                            Start development server" . PHP_EOL;
    echo "  dev-yann                       Start development server (without the -)" . PHP_EOL;
    echo "  db [start|stop|seed]           Start/Stop development database of seed the DB" . PHP_EOL;
    echo "  tw                             Start tailwind watcher" . PHP_EOL;
    echo "  sf                             Synchronize your fork" . PHP_EOL;
    echo "  cron                           Schedule tasks" . PHP_EOL;
    echo "  task [list|<task-name>]        List task or run specific task" . PHP_EOL;
}
