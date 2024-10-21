<?php

use app\core\Application;

class m0003_create_table_cb_mean_of_payement
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE cb_mean_of_payment (
            payment_id INT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            card_number VARCHAR(16) NOT NULL,
            expiration_date DATE NOT NULL,
            cvv VARCHAR(3) NOT NULL,
            CONSTRAINT cb_mean_of_payment_fk FOREIGN KEY (payment_id) REFERENCES mean_of_payment (id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE cb_mean_of_payment CASCADE;";
        $db->pdo->exec($sql);
    }
}