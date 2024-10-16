<?php

use app\core\Application;

class m0001_create_table_paypal_mean_of_paiement
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE paypal_mean_of_paiement (
            id INT PRIMARY KEY,
            paypal_url VARCHAR(255) NOT NULL,
            CONSTRAINT paypal_mean_of_paiement_paypal_fk FOREIGN KEY (id) REFERENCES mean_of_paiement (id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE paypal_mean_of_paiement;";
        $db->pdo->exec($sql);
    }
}