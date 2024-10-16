<?php

use app\core\Application;

class m0007_tarif_public{
    public function up(){
        $db = Application::$app->db;
        $sql = "CREATE TABLE tarif(
            id SERIAL PRIMARY KEY,
            denomination VARCHAR(255),
            price NUMERIC,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $db->pdo->exec($sql);
    }

    public function down(){
        $sql = "DROP TABLE tarif;";
        Application::$app-db->pdo->exec($sql);
    }
}