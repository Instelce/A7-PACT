<?php

namespace app\migrations;

use app\core\Application;

class m0030_create_table_performance
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE performance (
            performance_id SERIAL PRIMARY KEY,
            name VARCHAR(128) NOT NULL  
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE offer_period;";
        Application::$app->db->pdo->exec($sql);
    }
}