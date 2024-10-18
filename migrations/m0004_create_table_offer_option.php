<?php

use app\core\Application;

class m0004_create_table_offer_option {
    public function up(){
        $db = Application::$app->db;
        $sql = "CREATE TABLE offer_option (
            id SERIAL PRIMARY KEY,
            launch_date DATE NOT NULL,
            week_counter INT NOT NULL,
            duration INT NOT NULL,
            offer_id INT NOT NULL,
                          
            FOREIGN KEY (offer_id) REFERENCES offer(id) 
        );";
        $db->pdo->exec($sql);

    }

    public function down(){
        $sql = "DROP TABLE offer_option CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}