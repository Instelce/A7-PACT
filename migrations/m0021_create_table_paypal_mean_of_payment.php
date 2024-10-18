<?php

use app\core\Application;

class m0021_create_table_paypal_mean_of_payment
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE paypal_mean_of_payment (
            paypal_id INT PRIMARY KEY,
            paypal_url VARCHAR(255) NOT NULL,
            CONSTRAINT paypal_mean_of_payment_fk FOREIGN KEY (paypal_id) REFERENCES mean_of_payment (paiement_id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE paypal_mean_of_payment;";
        $db->pdo->exec($sql);
    }
}