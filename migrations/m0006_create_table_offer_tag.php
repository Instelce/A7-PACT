<?php

use app\core\Application;


class m0006_create_table_offer_tag
{
    public function up()
    {

        $db = Application::$app->db;
        $sql = "CREATE TABLE offer_tag (
            id SERIAL PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            offer_id INT  NOT NULL,
            
            FOREIGN KEY (offer_id) REFERENCES offer(id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE offer_tag CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}