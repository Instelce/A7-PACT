<?php

use app\core\Application;

class m0005_typeOffre{
    public function up(){
        $db = Application::$app->$db;
        $sql = "CREATE TABLE offre(
            id SERIAL PRIMARY KEY,
            type VARCHAR(255),
            prix NUMERIC,     
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $db->pdo->exec($sql);
    }

    public function down(){
        $sql = "DROP TABLE offre;";
        Application::$app->$db->pdo->exec($sql);
    }
}
