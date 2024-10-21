<?php

use app\core\Application;

class m0019_create_table_performance
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE performance (
            id SERIAL PRIMARY KEY,
            name VARCHAR(128) NOT NULL,
            
            offer_id INT NOT NULL,  
            
            FOREIGN KEY (offer_id) REFERENCES activity_offer (offer_id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE performance;";
        Application::$app->db->pdo->exec($sql);
    }
}