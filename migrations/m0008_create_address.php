<?php 

use app\core\Application;

class m0008_create_address {
    public function up(){
        $db = Application::$app->db;
        $sql = "CREATE TABLE address (
            id SERIAL PRIMARY KEY,
            number INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            city VARCHAR(255) NOT NULL,
            postal_code INT NOT NULL,
            longitude NUMERIC,
            latitude NUMERIC,
            offer_id INT NOT NULL,
                     
            FOREIGN KEY (offer_id) REFERENCES offer(id)
        );";
        $db->pdo->exec($sql);
    }

    public function down(){
        $sql = "DROP TABLE address;";
        Application::$app->db->pdo->exec($sql);
    }
}