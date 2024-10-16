<?php

namespace app\migrations;

use app\core\Application;

class m0028_create_table_offer_period
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE offer_period (
            id SERIAL PRIMARY KEY,
            
            start_date DATE NOT NULL,
            end_date DATE NOT NULL       
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE offer_period;";
        Application::$app->db->pdo->exec($sql);
    }
}