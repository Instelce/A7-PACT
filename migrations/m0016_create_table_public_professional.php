<?php

use app\core\Application;

class m0016_create_table_public_professional
{
    public function up() {
        $db = Application::$app->db;
        $sql = "CREATE TABLE public_professional (
            pu_pro_id INT PRIMARY KEY,
                
            CONSTRAINT public_professional_fk FOREIGN KEY (pu_pro_id) REFERENCES professional_user(pro_id)
        );";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE public_professional CASCADE;";
        $db->pdo->exec($sql);
    }
}