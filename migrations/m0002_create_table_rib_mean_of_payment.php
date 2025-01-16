<?php

use app\core\Application;

class m0002_create_table_rib_mean_of_payment
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE rib_mean_of_payment (
            payment_id INT PRIMARY KEY,
            titular_account VARCHAR(64) NOT NULL,
            iban VARCHAR(64) UNIQUE NOT NULL,
            bic VARCHAR(16) NOT NULL,
            CONSTRAINT rib_mean_of_payment_fk FOREIGN KEY (payment_id) REFERENCES mean_of_payment (id) ON DELETE CASCADE
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE rib_mean_of_payment CASCADE;";
        $db->pdo->exec($sql);
    }
}