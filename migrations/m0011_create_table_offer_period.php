<?php

use app\core\Application;

class m0011_create_table_offer_period
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE offer_period (
            id SERIAL PRIMARY KEY,
            offer_id INT NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL
        );
        ";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE offer_period CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}