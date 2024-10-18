<?php

namespace app\core;

class Database
{
    public \PDO $pdo;

    public function __construct(array $config)
    {
        $dsn = $config['dsn'] ?? '';  // domain service name
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';

        // Connect to the database with pdo
        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigration()
    {
        $this->createMigrationTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $newMigrations = [];
        $files = scandir(Application::$ROOT_DIR . "/migrations");
        $toApplyMigrations = array_diff($files, $appliedMigrations);

        foreach ($toApplyMigrations as $migration) {
            if ($migration === '.' || $migration === '..') {
                continue;
            }

            require_once Application::$ROOT_DIR . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className;

            $this->inlineLog("Migration $migration");
            try {
                $instance->up();
                echo " - Applied ✅" . PHP_EOL;
            } catch (\Exception $e) {
                echo " - Error ❌" . PHP_EOL;
                echo $e->getMessage() . PHP_EOL . PHP_EOL;
            }
            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log("All migrations are applied");
        }
    }

    public function createMigrationTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
                id SERIAL PRIMARY KEY,
                migration VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );");
    }

    public function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations)
    {
        $str = implode(",", array_map(fn($m) => "('$m')", $migrations));
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $str");
        $statement->execute();
    }

    public function dropMigrations() {
        $appliedMigrations = $this->getAppliedMigrations();

        $files = scandir(Application::$ROOT_DIR . "/migrations");

        foreach ($files as $migration) {
            if ($migration === '.' || $migration === '..') {
                continue;
            }

            require_once Application::$ROOT_DIR . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className();

            $instance->down();
            $this->log("Drop migration $migration");
        }

        $statement = $this->pdo->prepare("DROP TABLE migrations");
        $statement->execute();
    }

    protected function log($message)
    {
        echo '[' . date('Y-m-d H-i-s') . '] - ' . $message . PHP_EOL;
    }

    protected function inlineLog($message)
    {
        echo '[' . date('Y-m-d H-i-s') . '] - ' . $message;
    }

    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }
}