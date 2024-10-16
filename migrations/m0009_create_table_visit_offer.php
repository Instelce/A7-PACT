<?php

use app\core\Application;

class m0009_create_table_visit_offer {
    public function up(){
        $db = Application::$app->db;
        $sql = "CREATE TABLE visit_offer (
            id SERIAL PRIMARY KEY,
            duration NUMERIC NOT NULL,
            guide BOOLEAN NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $db->pdo->exec($sql);
    }

    public function down(){
        $sql = "DROP TABLE visit_offer;";
        Application::$app->db->pdo->exec($sql);
    }
}