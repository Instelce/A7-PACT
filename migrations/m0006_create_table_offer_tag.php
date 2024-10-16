<?php

use app\core\Application;

class m0006_create_table_offer_tag {
    public function up(){
        $db = Application::$app->db;
        $sql = "CREATE TABLE offer_tag (
            id SERIAL PRIMARY KEY,
            name INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $db->pdo->exec($sql);
    }

    public function down(){
        $sql = "DROP TABLE offer_tag;";
        Application::$app->db->pdo->exec($sql);
    }
}