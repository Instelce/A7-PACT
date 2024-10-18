<?php

use app\core\Application;

class m0019_create_table_rib_mean_of_paiement
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE rib_mean_of_paiement (
            rib_id INT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            iban VARCHAR(34) UNIQUE NOT NULL,
            bic VARCHAR(11) NOT NULL,
            CONSTRAINT rib_mean_of_paiement_fk FOREIGN KEY (rib_id) REFERENCES mean_of_paiement (paiement_id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE rib_mean_of_paiement;";
        $db->pdo->exec($sql);
    }
}