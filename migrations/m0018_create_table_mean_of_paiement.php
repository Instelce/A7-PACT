<?php

use app\core\Application;

class m0001_create_table_mean_of_paiement
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE mean_of_paiement (
            id_paiement SERIAL PRIMARY KEY,
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE mean_of_paiement;";
        $db->pdo->exec($sql);
    }
}