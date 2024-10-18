<?php

use app\core\Application;

class m0018_create_table_mean_of_payment
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE mean_of_payment (
            payment_id SERIAL PRIMARY KEY
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE mean_of_payment;";
        $db->pdo->exec($sql);
    }
}