<?php

use app\core\Application;

class m0005_create_table_offer_type {
    public function up(){
        $db = Application::$app->db;
        $sql = "CREATE TABLE offer_type (
            id SERIAL PRIMARY KEY,
            type VARCHAR(255) NOT NULL,
            price NUMERIC NOT NULL,
            offer_id INT NOT NULL,
                        
            FOREIGN KEY (offer_id) REFERENCES offer(id)
        );";
        $db->pdo->exec($sql);
    }

    public function down(){
        $sql = "DROP TABLE offer_type CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}
