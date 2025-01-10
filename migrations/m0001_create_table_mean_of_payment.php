<?php

use app\core\Application;

class m0001_create_table_mean_of_payment
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE mean_of_payment (
        id SERIAL PRIMARY KEY
    );";
        $db->pdo->exec($sql);
    }

    public function down() {
        $db = Application::$app->db;
        $sql = "DROP TABLE mean_of_payment CASCADE;";
        $db->pdo->exec($sql);
    }
}