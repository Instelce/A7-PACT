<?php

use app\core\Application;

class m0010_create_public_tariff {
    public function up(){
        $db = Application::$app->db;
        $sql = "CREATE TABLE public_tariff (
            id SERIAL PRIMARY KEY,
            denomination VARCHAR(255) NOT NULL,
            price NUMERIC NOT NULL,
            offer_id INT NOT NULL,
                           
            FOREIGN KEY (offer_id) REFERENCES offer(id)  
        );";
        $db->pdo->exec($sql);
    }

    public function down(){
        $sql = "DROP TABLE public_tariff CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}