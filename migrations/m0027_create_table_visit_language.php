<?php

namespace app\migrations;

use app\core\Application;

class m0027_create_table_visit_language
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE visit_language (
            id SERIAL PRIMARY KEY,
            offer_id INT NOT NULL,
            
            language VARCHAR(255) NOT NULL,
            
            FOREIGN KEY (offer_id) REFERENCES offer(id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE attraction_park_offer;";
        Application::$app->db->pdo->exec($sql);
    }
}