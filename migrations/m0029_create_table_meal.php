<?php

namespace app\migrations;

use app\core\Application;

class m0029_create_table_meal
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "CREATE TABLE meal (
            meal_id SERIAL PRIMARY KEY,
            name VARCHAR(30) NOT NULL  
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE offer_period;";
        Application::$app->db->pdo->exec($sql);
    }
}