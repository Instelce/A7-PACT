<?php

use app\core\Application;

class m0015_create_table_private_professional
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE private_professional (
            priv_pro_id INT PRIMARY KEY,
            
            last_veto DATE NOT NULL,
            
            CONSTRAINT private_professional_fk FOREIGN KEY (priv_pro_id) REFERENCES professional_user(pro_id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE private_professional;";
        $db->pdo->exec($sql);
    }
}