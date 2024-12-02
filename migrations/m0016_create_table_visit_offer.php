<?php

use app\core\Application;

// TODO : add language to offer

class m0016_create_table_visit_offer
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE visit_offer (
            offer_id INT NOT NULL PRIMARY KEY,

            duration NUMERIC NOT NULL,
            guide BOOLEAN NOT NULL,
            

            
            FOREIGN KEY (offer_id) REFERENCES offer(id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE visit_offer CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}