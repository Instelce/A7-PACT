<?php

use app\core\Application;

class m0003_create_table_offer_photo{
    public function up(){
        $db = Application::$app->db;
        $sql = "CREATE TABLE offer_photo (
            id SERIAL PRIMARY KEY,
            url_photo VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $db->pdo->exec($sql);
    }

    public function down(){
        $sql = "DROP TABLE offer_photo;";
        Application::$app->db->pdo->exec($sql);
    }
}