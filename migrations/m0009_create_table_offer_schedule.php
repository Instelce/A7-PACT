<?php

use app\core\Application;

class m0009_create_table_offer_schedule
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE offer_schedule (
            id SERIAL PRIMARY KEY,
            offer_id INT NOT NULL,
            
            day INT NOT NULL,
            opening_hours TIME NOT NULL,
            closing_hours TIME NOT NULL,
            
            FOREIGN KEY (offer_id) REFERENCES offer(id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE offer_schedule CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}