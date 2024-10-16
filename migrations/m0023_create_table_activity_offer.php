<?php

use app\core\Application;

class m0023_create_table_activity_offer
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE activity_offer (
            offer_id INT NOT NULL PRIMARY KEY,
            
            duration FLOAT NOT NULL,
            required_age INT NOT NULL,
            price FLOAT NOT NULL,
            
            FOREIGN KEY (offer_id) REFERENCES offer(id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE activity_offer;";
        Application::$app->db->pdo->exec($sql);
    }
}