<?php

use app\core\Application;

class m0007_create_public_tariff {
    public function up(){
        $db = Application::$app->db;
        $sql = "CREATE TABLE public_tariff (
            id SERIAL PRIMARY KEY,
            denomination VARCHAR(255) NOT NULL,
            price NUMERIC NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $db->pdo->exec($sql);
    }

    public function down(){
        $sql = "DROP TABLE public_tariff;";
        Application::$app->db->pdo->exec($sql);
    }
}