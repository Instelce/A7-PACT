<?php

use app\core\Application;

class m0009_visite{
    public function up(){
        $db = Application::$app->db;
        $sql = "CREATE TABLE visite(
            id SERIAL PRIMARY KEY,
            duree NUMERIC,
            guide BOOLEAN,
            langue VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );"; 
        $db->pdo->exec($sql);
    }

    public function down(){
        $sql = "DROP TABLE visite;";
        Application::$app->db->pdo->exec($sql);
    }
}