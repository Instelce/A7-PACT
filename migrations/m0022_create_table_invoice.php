<?php

use app\core\Application;

class m0022_create_table_invoice
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = " CREATE TABLE invoice (
             id SERIAL PRIMARY KEY,
             issue_date DATE NOT NULL,
             service_date INT NOT NULL,
             due_date DATE NOT NULL,
             offer_id INT NOT NULL,   
             FOREIGN KEY (offer_id) REFERENCES offer(id) ON DELETE CASCADE
        );
        CREATE TABLE offer_status_history (
            id SERIAL PRIMARY KEY,
            offer_id INT NOT NULL,
            switch_to VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (offer_id) REFERENCES offer(id) ON DELETE CASCADE
        );";
        $db->pdo->exec($sql);
    }
    public function down()
    {
        $sql = "DROP TABLE invoice CASCADE;
                DROP TABLE offer_status_history CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}