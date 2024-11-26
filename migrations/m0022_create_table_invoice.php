<?php

namespace app\migrations;

use app\core\Application;

class m0022_create_table_invoice
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = " CREATE TABLE invoice (
             id SERIAL PRIMARY KEY,
             issue_date DATE NOT NULL,
             service_date DATE NOT NULL,
             due_date DATE NOT NULL,
             professional_id INT NOT NULL,
             offer_id INT NOT NULL,   
             FOREIGN KEY (professional_id) REFERENCES account(id) ON DELETE CASCADE,
             FOREIGN KEY (offer_id) REFERENCES offer(id) ON DELETE CASCADE,    
        );";
        $db->pdo->exec($sql);
    }
    public function down()
    {
        $sql = "DROP TABLE invoice CASCADE;";
        Application::$app->db->pdo->exec($sql);
    }
}