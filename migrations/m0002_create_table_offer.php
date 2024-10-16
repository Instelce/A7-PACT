<?php

use app\core\Application;

class m0002_create_table_offer
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE offer (
            id SERIAL PRIMARY KEY,
            title VARCHAR(60) NOT NULL,
            summary VARCHAR(128) NOT NULL,
            description VARCHAR(1024) NOT NULL,
            likes INT DEFAULT 0,
            offline BOOLEAN DEFAULT TRUE,
            offline_date DATE,
            last_online_date DATE,
            view_counter INT DEFAULT 0,
            click_counter INT DEFAULT 0,
            website VARCHAR(255),
            phone_number VARCHAR(10),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $db->pdo->exec($sql);
    }

    public function down() {
        $db = Application::$app->db;
        $sql = "DROP TABLE offer;";
        $db->pdo->exec($sql);
    }
}