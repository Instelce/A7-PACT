<?php

use app\core\Application;

class m0026_create_table_show_offer
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE show_offer (
            offer_id INT NOT NULL PRIMARY KEY,
            
            duration FLOAT NOT NULL,
            capacity INT NOT NULL,
            
            FOREIGN KEY (offer_id) REFERENCES offer(id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE show_offer CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}