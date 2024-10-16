<?php

use app\core\Application;

class m0004_create_table_offer_option {
    public function up(){
        $db = Application::$app->db;
        $sql = "CREATE TABLE offer_option (
            id SERIAL PRIMARY KEY,
            launch_date DATE,
            week_counter INT,
            duration INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (offer) RREFERENCES 
        );";
        $db->pdo->exec($sql);
    }

    public function down(){
        $sql = "DROP TABLE offer_option;";
        Application::$app->db->pdo->exec($sql);
    }
}