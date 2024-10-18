<?php

use app\core\Application;

class m0001_create_table_account
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE account (
            id SERIAL PRIMARY KEY,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE account CASCADE;";
        $db->pdo->exec($sql);
    }
}