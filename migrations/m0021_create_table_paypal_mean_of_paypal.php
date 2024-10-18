<?php

use app\core\Application;

class m0021_create_table_paypal_mean_of_paypal
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE paypal_mean_of_paiement (
            paypal_id INT PRIMARY KEY,
            paypal_url VARCHAR(255) NOT NULL,
            CONSTRAINT paypal_mean_of_paiement_paypal_fk FOREIGN KEY (paypal_id) REFERENCES mean_of_paiement (paiement_id)
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