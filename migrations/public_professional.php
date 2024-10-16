<?php

use app\core\Application;

class _publicProfessional
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE comptes (
            id SERIAL PRIMARY KEY,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE comptes;";
        $db->pdo->exec($sql);
    }
}